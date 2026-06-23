<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('age')->nullable()->after('location');
            $table->string('gender')->nullable()->after('age');
            $table->string('fitness_goal')->nullable()->after('gender');
            $table->string('experience_level')->nullable()->after('fitness_goal');
            $table->string('budget_range')->nullable()->after('experience_level');
            $table->string('diet_preference')->nullable()->after('budget_range');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'age',
                'gender',
                'fitness_goal',
                'experience_level',
                'budget_range',
                'diet_preference',
            ]);
        });
    }
};
