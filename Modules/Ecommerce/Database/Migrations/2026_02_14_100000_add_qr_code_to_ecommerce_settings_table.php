<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQrCodeToEcommerceSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('ecommerce_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('ecommerce_settings', 'qr_code')) {
                $table->text('qr_code')->nullable()->after('gift_card');
            }
        });
    }

    public function down()
    {
        Schema::table('ecommerce_settings', function (Blueprint $table) {
            if (Schema::hasColumn('ecommerce_settings', 'qr_code')) {
                $table->dropColumn('qr_code');
            }
        });
    }
}
