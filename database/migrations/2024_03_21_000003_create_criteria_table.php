<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('criteria_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['code', 'criteria_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
}; 