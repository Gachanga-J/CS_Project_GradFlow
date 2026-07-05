<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->unsignedTinyInteger('year_of_study')
                  ->nullable()
                  ->after('intake_year')
                  ->comment('Target year of study e.g. 3 = Year 3 students');
        });
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('year_of_study');
        });
    }
};
