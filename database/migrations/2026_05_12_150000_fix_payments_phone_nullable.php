<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments') || ! Schema::hasColumn('payments', 'phone')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE payments MODIFY phone VARCHAR(255) NULL');
            DB::statement("ALTER TABLE payments MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");

            return;
        }

        Schema::table('payments', function ($table) {
            $table->string('phone')->nullable()->change();
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        //
    }
};
