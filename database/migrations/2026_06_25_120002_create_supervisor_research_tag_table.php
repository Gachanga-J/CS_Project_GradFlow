<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisor_research_tag', function (Blueprint $table) {
            $table->foreignId('supervisor_id')->constrained('supervisors')->onDelete('cascade');
            $table->foreignId('research_tag_id')->constrained('research_tags')->onDelete('cascade');
            $table->primary(['supervisor_id', 'research_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_research_tag');
    }
};
