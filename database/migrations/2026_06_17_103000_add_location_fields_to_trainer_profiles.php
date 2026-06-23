<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('trainer_profiles', 'county_id')) {
                $table->foreignId('county_id')->nullable()->after('user_id')->constrained('counties')->nullOnDelete();
            }

            if (! Schema::hasColumn('trainer_profiles', 'town')) {
                $table->string('town')->nullable()->after('county_id');
            }

            if (! Schema::hasColumn('trainer_profiles', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('town');
            }

            if (! Schema::hasColumn('trainer_profiles', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainer_profiles', function (Blueprint $table) {
            foreach (['longitude', 'latitude', 'town', 'county_id'] as $column) {
                if (Schema::hasColumn('trainer_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
