<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPosRowTypeAndPosSortOrderToProductSales extends Migration
{
    /**
     * Store POS row type and display order so edit shows exactly as create.
     * pos_row_type: 'display' | 'parent' | 'child'
     * pos_sort_order: 1, 2, 3... (form order)
     */
    public function up()
    {
        if (!Schema::hasColumn('product_sales', 'pos_row_type')) {
            Schema::table('product_sales', function (Blueprint $table) {
                $table->string('pos_row_type', 20)->nullable()->after('custom_parent_id')
                    ->comment('display=display row, parent=tray/box parent, child=inside tray/box');
            });
        }
        if (!Schema::hasColumn('product_sales', 'pos_sort_order')) {
            Schema::table('product_sales', function (Blueprint $table) {
                $table->unsignedInteger('pos_sort_order')->nullable()->after('pos_row_type')
                    ->comment('Display order in POS table (1,2,3...)');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('product_sales', 'pos_row_type')) {
            Schema::table('product_sales', function (Blueprint $table) {
                $table->dropColumn('pos_row_type');
            });
        }
        if (Schema::hasColumn('product_sales', 'pos_sort_order')) {
            Schema::table('product_sales', function (Blueprint $table) {
                $table->dropColumn('pos_sort_order');
            });
        }
    }
}
