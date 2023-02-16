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
        Schema::create('cds_item', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('CDのタイトル');
            $table->string('page_url')->comment('CDのタイトル');
            $table->string('genre')->comment('CDのジャンル');
            $table->string('genre_detail')->nullable()->comment('CDのジャンルの詳細');
            $table->string('author')->nullable()->comment('CDのアーティスト');
            $table->string('relation_item')->nullable()->comment('CDの関連作品');
            $table->string('selling_agency')->nullable()->comment('CDの発売元');
            $table->string('distributor')->nullable()->comment('CDの販売元');
            $table->string('disc_count')->nullable()->comment('CDのディスク枚数');
            $table->string('music_count')->nullable()->comment('CDの曲数');
            $table->string('record_time')->nullable()->comment('CDの収録時間');
            $table->string('cd_number')->nullable()->comment('CDの品番');
            $table->string('jan')->nullable()->comment('CDのJAN');
            $table->string('in_store_code')->nullable()->comment('CDのインストアコード');
            $table->string('release_date')->nullable()->comment('CDの発売日');
            $table->string('image_url')->nullable()->comment('CDの画像url');
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
        Schema::dropIfExists('cds_item');
    }
};
