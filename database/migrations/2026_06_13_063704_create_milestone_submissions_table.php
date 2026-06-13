<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestones')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('restrict');
            $table->string('file_path', 512);
            $table->string('file_name', 255);
            $table->unsignedInteger('file_size_bytes')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedSmallInteger('version_number')->default(1);
            $table->boolean('is_latest')->default(true);
            $table->enum('status', [
                'submitted',
                'supervisor_approved',
                'supervisor_rejected',
                'graded',
            ])->default('submitted');
            $table->text('supervisor_feedback')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->unsignedTinyInteger('grade')->nullable()->comment('Grade out of 100');
            $table->timestamp('submitted_at')->default(now());
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_submissions');
    }
};