<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            // Table exists, add missing columns if they don't exist
            Schema::table('payments', function (Blueprint $table) {
                if (! Schema::hasColumn('payments', 'phone')) {
                    $table->string('phone')->nullable()->after('membership_id');
                }
                if (! Schema::hasColumn('payments', 'receipt')) {
                    $table->string('receipt')->nullable()->after('phone');
                }
                if (! Schema::hasColumn('payments', 'trainer_id')) {
                    $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete()->after('membership_id');
                }
                if (! Schema::hasColumn('payments', 'gym_owner_id')) {
                    $table->foreignId('gym_owner_id')->nullable()->constrained('users')->nullOnDelete()->after('trainer_id');
                }
                if (! Schema::hasColumn('payments', 'mpesa_checkout_request_id')) {
                    $table->string('mpesa_checkout_request_id')->nullable()->unique()->after('reference');
                }
                if (! Schema::hasColumn('payments', 'mpesa_merchant_request_id')) {
                    $table->string('mpesa_merchant_request_id')->nullable()->after('mpesa_checkout_request_id');
                }
                if (! Schema::hasColumn('payments', 'transaction_code')) {
                    $table->string('transaction_code')->nullable()->after('mpesa_merchant_request_id');
                }
                if (! Schema::hasColumn('payments', 'mpesa_response')) {
                    $table->json('mpesa_response')->nullable()->after('transaction_code');
                }
            });
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gym_owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('receipt')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('method')->default('mpesa');
            $table->string('status')->default('pending');
            $table->string('reference')->nullable()->unique();
            $table->string('mpesa_checkout_request_id')->nullable()->unique();
            $table->string('mpesa_merchant_request_id')->nullable();
            $table->string('transaction_code')->nullable();
            $table->json('mpesa_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
