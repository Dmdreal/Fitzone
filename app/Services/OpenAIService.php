<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class OpenAIService
{
    public function chat(array $messages, string $model = null): ?array
    {
        $key = config('services.openai.key') ?: env('OPENAI_API_KEY');
        $model = $model ?? config('services.openai.model', 'gpt-4o-mini');

        if (! $key) {
            return null;
        }

        $response = Http::withToken($key)
            ->acceptJson()
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2,
                'max_tokens' => 800,
            ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    public function recommendPlan(array $clientData): ?string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a fitness assistant. Provide concise workout and diet recommendations.'],
            ['role' => 'user', 'content' => 'Client data: '.json_encode($clientData)],
        ];

        $json = $this->chat($messages);

        return Arr::get($json, 'choices.0.message.content');
    }

    public function rankTrainers(array $trainers, array $clientData): ?array
    {
        $trainerList = array_map(fn($t) => ['id' => $t['id'] ?? $t->id, 'name' => $t['name'] ?? ($t->user->name ?? null), 'specialization' => $t['specialization'] ?? ($t->specialization ?? null)], $trainers);

        $prompt = "Client: ".json_encode($clientData)."\n\nTrainers: ".json_encode($trainerList)."\n\nReturn a JSON array of trainer IDs ordered from best to worst with an optional reason for each.";

        $messages = [
            ['role' => 'system', 'content' => 'You are an assistant that ranks trainers for fitness clients. Respond only in JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $json = $this->chat($messages);

        $text = Arr::get($json, 'choices.0.message.content');
        if (! $text) {
            return null;
        }

        // Attempt to decode JSON from model output
        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return null;
    }
}
