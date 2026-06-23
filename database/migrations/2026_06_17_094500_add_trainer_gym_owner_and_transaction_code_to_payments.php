<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'trainer_id')) {
                $table->foreignId('trainer_id')->nullable()->after('membership_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'gym_owner_id')) {
                $table->foreignId('gym_owner_id')->nullable()->after('trainer_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'transaction_code')) {
                $table->string('transaction_code')->nullable()->after('mpesa_merchant_request_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'transaction_code')) {
                $table->dropColumn('transaction_code');
            }

            if (Schema::hasColumn('payments', 'gym_owner_id')) {
                $table->dropConstrainedForeignId('gym_owner_id');
            }

            if (Schema::hasColumn('payments', 'trainer_id')) {
                $table->dropConstrainedForeignId('trainer_id');
            }
        });
    }
};
