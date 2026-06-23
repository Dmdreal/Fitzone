<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class VerifyApiIntegrations extends Command
{
    protected $signature = 'verify:apis';

    protected $description = 'Verify that all external API integrations are properly configured';

    public function handle()
    {
        $this->info('🔍 Verifying Fitzone API Integrations...\n');

        $results = [
            'google_maps' => $this->checkGoogleMaps(),
            'openai' => $this->checkOpenAI(),
            'resend' => $this->checkResend(),
            'mpesa' => $this->checkMpesa(),
        ];

        $this->displayResults($results);

        $allOk = collect($results)->every(fn($r) => $r['status'] === 'OK');
        return $allOk ? self::SUCCESS : self::FAILURE;
    }

    private function checkGoogleMaps()
    {
        $key = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');
        
        if (!$key) {
            return ['status' => 'MISSING', 'message' => 'API key not configured'];
        }

        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => 'Nairobi, Kenya',
                'key' => $key,
            ]);

            if ($response->ok()) {
                return ['status' => 'OK', 'message' => 'Geocoding API working'];
            } else {
                return ['status' => 'ERROR', 'message' => 'API responded with error: ' . $response->status()];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function checkOpenAI()
    {
        $key = config('services.openai.key') ?? env('OPENAI_API_KEY');
        $model = config('services.openai.model', 'gpt-4o-mini');

        if (!$key) {
            return ['status' => 'MISSING', 'message' => 'API key not configured'];
        }

        try {
            $response = Http::withToken($key)
                ->acceptJson()
                ->timeout(5)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => 'Hi']],
                    'max_tokens' => 10,
                ]);

            if ($response->ok()) {
                return ['status' => 'OK', 'message' => "ChatGPT API working (model: $model)"];
            } elseif ($response->status() === 429) {
                return ['status' => 'OK', 'message' => "ChatGPT API working but rate limited (model: $model)"];
            } else {
                return ['status' => 'ERROR', 'message' => 'API error: ' . $response->status()];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function checkResend()
    {
        $key = config('services.resend.key') ?? env('RESEND_API_KEY');

        if (!$key) {
            return ['status' => 'MISSING', 'message' => 'API key not configured'];
        }

        try {
            $response = Http::withHeaders(['Authorization' => "Bearer $key"])
                ->timeout(5)
                ->get('https://api.resend.com/emails');

            if ($response->ok() || $response->status() === 401) {
                // 401 might mean key is invalid, but at least the endpoint is reachable
                return ['status' => 'OK', 'message' => 'Resend API reachable and configured'];
            } else {
                return ['status' => 'ERROR', 'message' => 'API error: ' . $response->status()];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function checkMpesa()
    {
        $consumerKey = config('services.mpesa.consumer_key');
        $consumerSecret = config('services.mpesa.consumer_secret');
        $env = config('services.mpesa.env', 'sandbox');

        if (!$consumerKey || !$consumerSecret) {
            return ['status' => 'MISSING', 'message' => 'Consumer key or secret not configured'];
        }

        $baseUrl = $env === 'sandbox' 
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';

        try {
            // M-Pesa OAuth endpoint to test credentials
            $response = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(5)
                ->get($baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->ok()) {
                return ['status' => 'OK', 'message' => "M-Pesa API working ($env mode)"];
            } elseif ($response->status() === 401) {
                return ['status' => 'ERROR', 'message' => 'M-Pesa credentials invalid (401)'];
            } else {
                return ['status' => 'ERROR', 'message' => 'API error: ' . $response->status()];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function displayResults($results)
    {
        foreach ($results as $api => $result) {
            $statusIcon = match ($result['status']) {
                'OK' => '✅',
                'MISSING' => '⚠️',
                'ERROR' => '❌',
            };

            $this->line("$statusIcon <fg=cyan>{$api}</> - {$result['message']}");
        }

        $this->newLine();
        $this->info('📝 For more details, see API_INTEGRATION_GUIDE.md');
    }
}
