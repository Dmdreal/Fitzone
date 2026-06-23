<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('preferred_package_id')->nullable()->constrained('membership_packages')->nullOnDelete()->after('verification_status');
            $table->decimal('preferred_rate', 10, 2)->nullable()->after('preferred_package_id');
        });

        Schema::table('trainer_profiles', function (Blueprint $table) {
            $table->foreignId('preferred_package_id')->nullable()->constrained('membership_packages')->nullOnDelete()->after('experience_years');
            $table->decimal('preferred_rate', 10, 2)->nullable()->after('preferred_package_id');
        });

        Schema::table('memberships', function (Blueprint $table) {
            if (! Schema::hasColumn('memberships', 'gym_owner_id')) {
                $table->foreignId('gym_owner_id')->nullable()->constrained('users')->nullOnDelete()->after('trainer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            if (Schema::hasColumn('memberships', 'gym_owner_id')) {
                $table->dropConstrainedForeignId('gym_owner_id');
            }
        });

        Schema::table('trainer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('trainer_profiles', 'preferred_rate')) {
                $table->dropColumn('preferred_rate');
            }
            if (Schema::hasColumn('trainer_profiles', 'preferred_package_id')) {
                $table->dropConstrainedForeignId('preferred_package_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'preferred_rate')) {
                $table->dropColumn('preferred_rate');
            }
            if (Schema::hasColumn('users', 'preferred_package_id')) {
                $table->dropConstrainedForeignId('preferred_package_id');
            }
        });
    }
};
