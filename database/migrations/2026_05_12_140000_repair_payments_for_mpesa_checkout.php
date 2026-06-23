<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'member_id')) {
                $table->foreignId('member_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('payments', 'membership_id')) {
                $table->foreignId('membership_id')->nullable()->after('member_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'trainer_id')) {
                $table->foreignId('trainer_id')->nullable()->after('membership_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'gym_owner_id')) {
                $table->foreignId('gym_owner_id')->nullable()->after('trainer_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'method')) {
                $table->string('method')->default('mpesa')->after('amount');
            }

            if (! Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable()->after('status');
            }

            if (! Schema::hasColumn('payments', 'mpesa_checkout_request_id')) {
                $table->string('mpesa_checkout_request_id')->nullable()->after('reference');
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

            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('mpesa_response');
            }

            if (! Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach ([
                'notes',
                'paid_at',
                'mpesa_response',
                'transaction_code',
                'mpesa_merchant_request_id',
                'mpesa_checkout_request_id',
                'reference',
                'method',
                'gym_owner_id',
                'trainer_id',
                'membership_id',
                'member_id',
            ] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
