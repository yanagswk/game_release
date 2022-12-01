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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('ゲームタイトル');
            $table->string('hardware', 30)->comment('ハードウェア');
            $table->integer('price')->nullable()->comment('価格');
            $table->string('sales_date')->nullable()->comment('発売日');
            $table->string('large_image_url')->nullable()->comment('画像URL');
            $table->string('item_url')->nullable()->comment('商品URL');
            $table->string('label')->nullable()->comment('発売元');
            $table->string('item_caption', 3000)->nullable()->comment('ゲーム説明');
            $table->integer('review_count')->nullable()->comment('レビュー件数');
            $table->double('review_average')->nullable()->comment('レビュー平均');
            $table->boolean('disabled')->default(false)->comment('無効フラグ');
            $table->timestamps();

            $table->unique(['title','hardware'], 'UNIQUE_COLUMNS'); // ユニーク制約
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
