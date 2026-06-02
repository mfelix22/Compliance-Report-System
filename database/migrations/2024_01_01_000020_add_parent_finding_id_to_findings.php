<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->foreignId('parent_finding_id')
                ->nullable()
                ->after('inspection_id')
                ->constrained('findings')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Finding::class, 'parent_finding_id');
            $table->dropColumn('parent_finding_id');
        });
    }
};
