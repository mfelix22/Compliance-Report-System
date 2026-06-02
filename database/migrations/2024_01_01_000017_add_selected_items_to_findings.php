<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            // Stores JSON array of policy_item ids that were checked (NC multi-select)
            $table->json('selected_policy_item_ids')->nullable()->after('policy_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropColumn('selected_policy_item_ids');
        });
    }
};
