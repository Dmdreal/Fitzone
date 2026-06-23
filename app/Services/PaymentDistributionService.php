<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentDistributed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class PaymentDistributionService
{
    public function distribute(Payment $payment): void
    {
        if ($payment->status !== 'paid') {
            return;
        }

        $membership = $payment->membership?->fresh(['gymOwner', 'trainer']);
        if (! $membership) {
            Log::warning('Payment distribution skipped; payment has no membership.', ['payment_id' => $payment->id]);
            return;
        }

        $amount = (float) $payment->amount;
        $gymShare = round($amount * 0.70, 2);
        $trainerShare = round($amount * 0.20, 2);
        $platformShare = round($amount - $gymShare - $trainerShare, 2);

        try {
            if ($membership->gymOwner) {
                $this->creditUserWallet($membership->gymOwner, $gymShare);
            }

            if ($membership->trainer) {
                $this->creditUserWallet($membership->trainer, $trainerShare);
            }

            $platformUser = User::where('role', 'admin')->first();
            if ($platformUser) {
                $this->creditUserWallet($platformUser, $platformShare);
            } else {
                Log::warning('Platform wallet share could not be allocated; admin user not found.', [
                    'payment_id' => $payment->id,
                    'platform_share' => $platformShare,
                ]);
            }

            $payment->notes = trim(($payment->notes ?? '')."\nDistribution - gym: {$gymShare}, trainer: {$trainerShare}, platform: {$platformShare}");
            $payment->save();

            // send notifications to recipients
            if ($membership->gymOwner) {
                $membership->gymOwner->notify(new PaymentDistributed($payment, 'gym', $gymShare));
            }

            if ($membership->trainer) {
                $membership->trainer->notify(new PaymentDistributed($payment, 'trainer', $trainerShare));
            }

            if ($platformUser) {
                $platformUser->notify(new PaymentDistributed($payment, 'platform', $platformShare));
            }
        } catch (Throwable $exception) {
            Log::error('Failed to distribute payment shares.', [
                'payment_id' => $payment->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function creditUserWallet(User $user, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $wallet = $user->wallet()->firstOrCreate(['member_id' => $user->id], ['balance' => 0]);
        $wallet->balance = (float) $wallet->balance + $amount;
        $wallet->save();
    }
}
