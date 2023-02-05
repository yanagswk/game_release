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
            $table->string('artist_name')->nullable()->comment('アーティスト名');
            $table->string('artist_name_kana')->nullable()->comment('アーティストかな');
            $table->string('label')->comment('発売元名');
            $table->string('play_list', 2000)->nullable()->comment('曲名');
            $table->integer('size')->nullable()->comment('CD区分 1:アルバム 2:シングル 3:ミニアルバム');
            $table->integer('price')->nullable()->comment('価格');
            $table->string('sales_date')->nullable()->comment('発売日');
            $table->string('large_image_url')->nullable()->comment('画像URL');
            $table->string('item_url')->nullable()->comment('商品URL');
            $table->string('affiliate_url')->nullable()->comment('アフィリエイトurl');
            $table->integer('review_count')->nullable()->comment('レビュー件数');
            $table->double('review_average')->nullable()->comment('レビュー平均');
            $table->string('item_caption', 3000)->nullable()->comment('説明');
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
