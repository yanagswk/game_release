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
            $table->string('series')->nullable()->comment('シリーズ');
            $table->string('size')->comment('本の種類');
            $table->integer('price')->nullable()->comment('価格');
            $table->string('sales_date')->nullable()->comment('発売日');
            $table->string('large_image_url')->nullable()->comment('画像URL');
            $table->string('item_url')->nullable()->comment('商品URL');
            $table->string('affiliate_url')->nullable()->comment('アフィリエイトurl');
            $table->string('author')->nullable()->comment('著者名');
            $table->string('publisherName')->nullable()->comment('出版社名');
            $table->integer('review_count')->nullable()->comment('レビュー件数');
            $table->double('review_average')->nullable()->comment('レビュー平均');
            $table->string('item_caption', 3000)->nullable()->comment('説明');
            $table->string('type')->nullable()->comment('本のタイプ');
            $table->string('contents')->nullable()->comment('図鑑や全集など複数巻からなる本の内容');
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
