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
        Schema::create('game_article', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id')->comment('記事サイトのid');
            $table->string('site_url', 255)->comment('記事のurl');
            $table->string('title', 255)->comment('記事のタイトル');
            $table->string('genre', 100)->nullable()->comment('記事のジャンル');
            $table->string('top_image_url', 255)->nullable()->comment('記事のトップ画像url');
            $table->dateTime('post_date')->comment('投稿日');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_article');
    }
};
