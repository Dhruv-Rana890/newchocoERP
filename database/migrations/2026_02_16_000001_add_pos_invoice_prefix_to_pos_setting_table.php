<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * POS invoice reference format: {prefix}-{year}-{sequence} e.g. BDR-2026-1
     */
    public function up(): void
    {
        Schema::table('pos_setting', function (Blueprint $table) {
            $table->string('pos_invoice_prefix', 50)->nullable()->default('BDR')->after('show_print_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_setting', function (Blueprint $table) {
            $table->dropColumn('pos_invoice_prefix');
        });
    }
};
