<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: multiple auditors per inspection
        Schema::create('inspection_auditors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['inspection_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_auditors');
    }
};
