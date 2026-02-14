<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hero banners: slider type (image/video), video_url, multiple images (JSON)
        if (Schema::hasTable('homepage_hero_banners')) {
            Schema::table('homepage_hero_banners', function (Blueprint $table) {
                if (!Schema::hasColumn('homepage_hero_banners', 'slider_type')) {
                    $table->string('slider_type')->default('image')->after('image');
                }
                if (!Schema::hasColumn('homepage_hero_banners', 'video_url')) {
                    $table->string('video_url')->nullable()->after('slider_type');
                }
                if (!Schema::hasColumn('homepage_hero_banners', 'images')) {
                    $table->json('images')->nullable()->after('video_url'); // ["img1.jpg","img2.jpg"] for multi-image slider
                }
            });
        }

        // Ecommerce settings: header announcement with close
        if (Schema::hasTable('ecommerce_settings')) {
            Schema::table('ecommerce_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ecommerce_settings', 'header_announcement')) {
                    $table->text('header_announcement')->nullable();
                }
                if (!Schema::hasColumn('ecommerce_settings', 'header_announcement_ar')) {
                    $table->text('header_announcement_ar')->nullable();
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('homepage_hero_banners')) {
            Schema::table('homepage_hero_banners', function (Blueprint $table) {
                $columns = ['slider_type', 'video_url', 'images'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('homepage_hero_banners', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
        if (Schema::hasTable('ecommerce_settings')) {
            Schema::table('ecommerce_settings', function (Blueprint $table) {
                if (Schema::hasColumn('ecommerce_settings', 'header_announcement')) {
                    $table->dropColumn('header_announcement');
                }
                if (Schema::hasColumn('ecommerce_settings', 'header_announcement_ar')) {
                    $table->dropColumn('header_announcement_ar');
                }
            });
        }
    }
};
