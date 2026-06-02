<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->foreignId('parent_inspection_id')
                ->nullable()
                ->after('notes')
                ->constrained('inspections')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Inspection::class, 'parent_inspection_id');
            $table->dropColumn('parent_inspection_id');
        });
    }
};
