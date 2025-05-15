<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_visit_id')->constrained()->onDelete('cascade');
            $table->string('criteria_group_code');
            $table->string('criterion_code');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('mark_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mark_id')->constrained()->onDelete('cascade');
            $table->string('status'); // corresponds, partially, not_corresponds, needs_clarification, not_applicable
            $table->dateTime('fixation_date');
            $table->date('regulation_date')->nullable();
            $table->text('state');
            $table->timestamps();
        });

        Schema::create('mark_stage_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mark_stage_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mark_stage_photos');
        Schema::dropIfExists('mark_stages');
        Schema::dropIfExists('marks');
    }
}; 