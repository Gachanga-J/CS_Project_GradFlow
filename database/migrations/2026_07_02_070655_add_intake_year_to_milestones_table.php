<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->unsignedSmallInteger('intake_year')
                  ->nullable()
                  ->after('department_id')
                  ->comment('Target cohort intake year e.g. 2022. Null = all cohorts.');
        });
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('intake_year');
        });
    }
};
