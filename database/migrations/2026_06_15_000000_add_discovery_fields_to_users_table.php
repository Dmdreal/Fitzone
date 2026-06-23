<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location')->nullable()->after('bio');
            $table->text('nearby_locations')->nullable()->after('location');
            $table->string('gym_name')->nullable()->after('nearby_locations');
            $table->text('gym_services')->nullable()->after('gym_name');
            $table->string('verification_status')->default('pending')->after('gym_services');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'nearby_locations',
                'gym_name',
                'gym_services',
                'verification_status',
            ]);
        });
    }
};
