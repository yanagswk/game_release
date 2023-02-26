<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dvd_blu_ray_item', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('タイトル');
            $table->string('page_url')->comment('タイトル');
            $table->integer('type')->nullable()->comment('DVD/Blu-ray');
            $table->string('genre')->comment('ジャンル');
            $table->string('genre_detail')->nullable()->comment('ジャンルの詳細');
            $table->string('author')->nullable()->comment('アーティスト');
            $table->string('relation_item')->nullable()->comment('関連作品');
            $table->string('selling_agency')->nullable()->comment('発売元');
            $table->string('distributor')->nullable()->comment('販売元');
            $table->string('disc_count')->nullable()->comment('ディスク枚数');
            // $table->string('music_count')->nullable()->comment('曲数');
            $table->string('record_time')->nullable()->comment('収録時間');
            $table->string('cd_number')->nullable()->comment('品番');
            $table->string('jan')->nullable()->comment('JAN');
            // $table->string('in_store_code')->nullable()->comment('インストアコード');
            $table->string('release_date')->nullable()->comment('発売日');
            $table->string('image_url')->nullable()->comment('画像url');
            $table->string('description', 3000)->nullable()->comment('商品説明');
            $table->string('rakuten_affiliate_url', 2000)->nullable()->comment('楽天アフィリエイトリンク');
            $table->string('amazon_affiliate_url', 2000)->nullable()->comment('Amazonアフィリエイトリンク');
            $table->boolean('disabled')->default(false)->comment('無効フラグ');
            $table->timestamps();

            $table->unique(['title'], 'UNIQUE_COLUMNS'); // ユニーク制約
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dvd_blu_ray_item');
    }
};
