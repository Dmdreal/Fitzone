<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_path')->nullable()->after('status');
            $table->string('phone')->nullable()->after('profile_photo_path');
            $table->string('headline')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('headline');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_photo_path', 'phone', 'headline', 'bio']);
        });
    }
};
