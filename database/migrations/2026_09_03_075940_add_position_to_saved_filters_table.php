<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('saved_filters', function (Blueprint $table) {
            $table->unsignedInteger('position')->default(0)->after('is_default');
            $table->index(['view', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_filters', function (Blueprint $table) {
            $table->dropIndex(['view', 'position']);
            $table->dropColumn('position');
        });
    }
};
