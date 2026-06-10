<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE client_chats MODIFY type ENUM('package_group', 'trainer_direct', 'member_direct') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE client_chats MODIFY type ENUM('package_group', 'trainer_direct') NOT NULL");
    }
};
