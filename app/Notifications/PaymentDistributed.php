<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentDistributed extends Notification
{
    use Queueable;

    private Payment $payment;
    private string $role;
    private float $amount;

    public function __construct(Payment $payment, string $role, float $amount)
    {
        $this->payment = $payment;
        $this->role = $role;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'role' => $this->role,
            'amount' => $this->amount,
            'notes' => $this->payment->notes,
        ];
    }
}
