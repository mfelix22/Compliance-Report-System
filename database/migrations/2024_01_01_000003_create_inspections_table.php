<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('reference_no')->unique();
            $table->date('inspection_date');
            $table->foreignId('auditor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['open', 'closed', 'in_review'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
