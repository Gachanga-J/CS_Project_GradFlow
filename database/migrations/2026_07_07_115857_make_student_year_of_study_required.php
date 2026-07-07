<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Safety check: refuse to run if any existing student has a null
        // year_of_study, since every scoping query in the app treats this
        // as required alongside intake_year, and such students would be
        // permanently unable to receive or submit to any milestone.
        $orphaned = DB::table('students')
            ->whereNull('year_of_study')
            ->count();

        if ($orphaned > 0) {
            throw new \RuntimeException(
                "Cannot make year_of_study required: {$orphaned} student(s) "
                . "have a null value. Fix them first, then re-run this migration."
            );
        }

        Schema::table('students', function (Blueprint $table) {
            $table->unsignedTinyInteger('year_of_study')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedTinyInteger('year_of_study')->nullable()->change();
        });
    }
};
