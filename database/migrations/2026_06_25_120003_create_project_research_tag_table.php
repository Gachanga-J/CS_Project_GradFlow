<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_research_tag', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('research_tag_id')->constrained('research_tags')->onDelete('cascade');
            $table->primary(['project_id', 'research_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_research_tag');
    }
};
