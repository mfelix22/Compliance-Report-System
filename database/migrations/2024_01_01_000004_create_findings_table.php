<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->unsignedInteger('number');                          // row No.
            $table->text('finding');                                    // Findings (Auditor)
            $table->enum('root_cause', ['people', 'facilities', 'training', 'others']); // Root Cause
            $table->foreignId('department_id')->constrained('departments'); // Dept Responsible
            // Auditee fills these
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open'); // Open/Closed
            $table->date('date_closed')->nullable();
            // Auditor verifies these
            $table->enum('verification_status', ['complied', 'not_complied', 'pending'])->default('pending');
            $table->date('verification_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
