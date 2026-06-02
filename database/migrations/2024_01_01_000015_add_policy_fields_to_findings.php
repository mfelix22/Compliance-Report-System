<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->foreignId('inspection_policy_id')->nullable()->after('inspection_id')->constrained()->nullOnDelete();
            $table->foreignId('policy_item_id')->nullable()->after('inspection_policy_id')->constrained()->nullOnDelete();
            $table->string('photo')->nullable()->after('department_id'); // required on new findings
            $table->text('keterangan')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropForeign(['inspection_policy_id']);
            $table->dropForeign(['policy_item_id']);
            $table->dropColumn(['inspection_policy_id', 'policy_item_id', 'photo', 'keterangan']);
        });
    }
};
