<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['code', 'template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria_groups');
    }
}; 