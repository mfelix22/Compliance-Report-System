<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->string('audit_time', 10)->nullable()->after('inspection_date'); // e.g. "09:30"
            $table->string('reporter_name')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropColumn(['audit_time', 'reporter_name']);
        });
    }
};
