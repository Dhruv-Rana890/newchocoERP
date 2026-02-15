<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowInMenuToCategoriesTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'show_in_menu')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->boolean('show_in_menu')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'show_in_menu')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('show_in_menu');
            });
        }
    }
}
