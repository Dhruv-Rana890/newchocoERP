<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMenuSortOrderToCategoriesTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'menu_sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('menu_sort_order')->nullable()->after('show_in_menu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'menu_sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('menu_sort_order');
            });
        }
    }
}
