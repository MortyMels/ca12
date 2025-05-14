<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // planned, unplanned
            $table->foreignId('template_id')->constrained()->onDelete('restrict');
            $table->foreignId('organization_id')->constrained()->onDelete('restrict');
            $table->string('status'); // planned, in_progress, completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
}; 