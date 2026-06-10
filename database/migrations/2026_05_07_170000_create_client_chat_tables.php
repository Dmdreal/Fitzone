<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_chats', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['package_group', 'trainer_direct']);
            $table->string('title');
            $table->foreignId('membership_package_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['type', 'membership_package_id'], 'unique_package_chat');
            $table->unique(['type', 'member_id', 'trainer_id'], 'unique_trainer_chat');
        });

        Schema::create('client_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_chat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_chat_messages');
        Schema::dropIfExists('client_chats');
    }
};
