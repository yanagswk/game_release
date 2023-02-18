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
        Schema::create('games_item', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('ゲームのタイトル');
            $table->string('page_url')->comment('詳細画面url');
            $table->string('genre')->comment('ゲームのジャンル');
            $table->string('genre_detail')->nullable()->comment('ゲームのジャンル詳細');
            $table->string('release_date')->nullable()->comment('ゲームの発売日');
            $table->string('relation_item')->nullable()->comment('ゲームの関連作品');
            $table->string('distributor')->nullable()->comment('CDの販売元');
            $table->string('game_number')->nullable()->comment('品番');
            $table->string('jan')->nullable()->comment('CDのJAN');
            $table->string('cero')->nullable()->comment('CDのJAN');
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
        Schema::dropIfExists('games_item');
    }
};
