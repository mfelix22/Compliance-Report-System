<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_category_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_policy_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['C', 'NC', 'NA']); // Compliant / Non-Compliant / Not Available
            $table->timestamps();

            $table->unique(['inspection_id', 'inspection_policy_id'], 'ics_inspection_policy_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_category_statuses');
    }
};
