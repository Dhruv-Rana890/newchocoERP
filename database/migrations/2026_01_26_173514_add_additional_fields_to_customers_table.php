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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('mobile_number_2')->nullable()->after('phone_number');
            $table->string('area')->nullable()->after('address');
            $table->string('house_number')->nullable()->after('area');
            $table->string('street')->nullable()->after('house_number');
            $table->string('ave')->nullable()->after('street');
            $table->string('block')->nullable()->after('ave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['mobile_number_2', 'area', 'house_number', 'street', 'ave', 'block']);
        });
    }
};
