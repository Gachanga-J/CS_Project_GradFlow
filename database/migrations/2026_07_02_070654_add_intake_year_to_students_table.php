<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedSmallInteger('intake_year')
                  ->nullable()
                  ->after('year_of_study')
                  ->comment('Year the student was enrolled e.g. 2022');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('intake_year');
        });
    }
};
