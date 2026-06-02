<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_policies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);          // I, II, ..., XV, LAIN, KONTAMINASI
            $table->string('name');
            $table->unsignedTinyInteger('due_date_offset_days')->default(7); // D+1, D+2, D+7
            $table->unsignedSmallInteger('score')->default(0);               // for scoring later
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_policies');
    }
};
