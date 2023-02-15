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
        Schema::create('books_item', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('本のタイトル');
            $table->string('page_url')->comment('詳細ページurl');
            $table->string('genre')->nullable()->comment('本のジャンル');
            $table->string('genre_detail')->nullable()->comment('本のジャンル詳細');
            $table->string('label')->nullable()->comment('レーベル');
            $table->string('author')->nullable()->comment('著者名');
            $table->string('image_url')->nullable()->comment('画像URL');
            $table->string('series')->nullable()->comment('シリーズ');
            $table->string('page', 10)->nullable()->comment('ページ数');
            $table->string('size', 10)->nullable()->comment('発行形態');
            $table->string('description', 3000)->nullable()->comment('商品説明');
            $table->string('release_date')->nullable()->comment('発売日');
            $table->string('publisher')->nullable()->comment('出版社名');
            $table->string('isbn')->nullable()->comment('本のISBN');
            $table->string('rakuten_affiliate_url', 2000)->nullable()->comment('楽天アフィリエイトリンク');
            $table->string('amazon_affiliate_url', 2000)->nullable()->comment('amazonアフィリエイトリンク');
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
        Schema::dropIfExists('books_item');
    }
};
