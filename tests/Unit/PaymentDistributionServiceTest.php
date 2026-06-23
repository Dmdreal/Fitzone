<?php

namespace Tests\Unit;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentDistributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentDistributionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_distribute_creates_wallet_shares()
    {
        // create users
        $gymOwner = User::factory()->create(['role' => 'gym']);
        $trainer = User::factory()->create(['role' => 'trainer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        // create a membership package and membership
        $package = \App\Models\MembershipPackage::create([
            'name' => 'Test Package',
            'slug' => 'test-package',
            'duration_unit' => 'month',
            'duration_count' => 1,
            'price' => 1000,
            'access_level' => 'standard',
            'trainer_access' => true,
            'benefits' => [],
            'is_active' => true,
        ]);

        $membership = Membership::create([
            'member_id' => $member->id,
            'membership_package_id' => $package->id,
            'trainer_id' => $trainer->id,
            'gym_owner_id' => $gymOwner->id,
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addMonth()->toDateString(),
            'status' => 'active',
        ]);

        $payment = Payment::create([
            'member_id' => $member->id,
            'membership_id' => $membership->id,
            'amount' => 1000,
            'method' => 'mpesa',
            'status' => 'paid',
        ]);

        (new PaymentDistributionService())->distribute($payment->fresh());

        $this->assertDatabaseHas('wallets', ['member_id' => $gymOwner->id, 'balance' => 700.00]);
        $this->assertDatabaseHas('wallets', ['member_id' => $trainer->id, 'balance' => 200.00]);
        $this->assertDatabaseHas('wallets', ['member_id' => $admin->id, 'balance' => 100.00]);
    }
}
