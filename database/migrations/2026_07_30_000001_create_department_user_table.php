<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'department_id']);
        });

        // Backfill: copy each user's existing single department into the pivot table.
        $now = now();
        $rows = DB::table('users')
            ->whereNotNull('department_id')
            ->get(['id', 'department_id']);

        foreach ($rows as $row) {
            DB::table('department_user')->insert([
                'user_id'       => $row->id,
                'department_id' => $row->department_id,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('department_user');
    }
};
