<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('duration_unit', ['day', 'week', 'month', 'year']);
            $table->unsignedSmallInteger('duration_count')->default(1);
            $table->decimal('price', 10, 2);
            $table->string('access_level')->default('standard');
            $table->boolean('trainer_access')->default(false);
            $table->json('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('trainer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('specialty');
            $table->string('category')->default('strength');
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->unsignedSmallInteger('experience_years')->default(1);
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_package_id')->constrained()->restrictOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['mpesa', 'card', 'bank', 'cash']);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('reference')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('attendance_date');
            $table->time('check_in_at')->nullable();
            $table->time('check_out_at')->nullable();
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->string('qr_code')->nullable();
            $table->timestamps();
            $table->unique(['member_id', 'attendance_date']);
        });

        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('focus_area')->nullable();
            $table->text('notes')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_plan_id')->constrained()->cascadeOnDelete();
            $table->string('exercise_name');
            $table->unsignedSmallInteger('sets')->default(3);
            $table->unsignedSmallInteger('reps')->default(10);
            $table->text('instructions')->nullable();
            $table->text('trainer_notes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('goal', ['weight_loss', 'muscle_gain', 'maintenance']);
            $table->unsignedSmallInteger('daily_calories')->nullable();
            $table->json('meal_schedule')->nullable();
            $table->boolean('meal_delivery_available')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('progress_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('recorded_at');
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('body_fat_percentage', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('gym_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('serial_number')->nullable()->unique();
            $table->enum('status', ['available', 'maintenance', 'retired'])->default('available');
            $table->date('last_serviced_at')->nullable();
            $table->date('next_service_at')->nullable();
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code')->unique();
            $table->enum('status', ['pending', 'converted', 'rewarded'])->default('pending');
            $table->unsignedInteger('reward_points')->default(0);
            $table->timestamps();
        });

        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->integer('points');
            $table->string('source');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('gym_equipment');
        Schema::dropIfExists('progress_records');
        Schema::dropIfExists('diet_plans');
        Schema::dropIfExists('workout_exercises');
        Schema::dropIfExists('workout_plans');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('trainer_profiles');
        Schema::dropIfExists('membership_packages');
    }
};
