<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Safety check: refuse to run if any existing milestone has a null
        // intake_year or year_of_study, since every query in the app treats
        // these as required and such rows would be silently invisible.
        $orphaned = DB::table('milestones')
            ->whereNull('intake_year')
            ->orWhereNull('year_of_study')
            ->count();

        if ($orphaned > 0) {
            throw new \RuntimeException(
                "Cannot make intake_year/year_of_study required: {$orphaned} milestone(s) "
                . "have a null value in one of these columns. Fix or delete them first, "
                . "then re-run this migration."
            );
        }

        Schema::table('milestones', function (Blueprint $table) {
            $table->unsignedSmallInteger('intake_year')->nullable(false)->change();
            $table->unsignedTinyInteger('year_of_study')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->unsignedSmallInteger('intake_year')->nullable()->change();
            $table->unsignedTinyInteger('year_of_study')->nullable()->change();
        });
    }
};
