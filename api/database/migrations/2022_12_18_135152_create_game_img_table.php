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
        Schema::create('game_image', function (Blueprint $table) {
            $table->id();
            $table->integer('game_id')->comment('通知設定するゲームid');
            $table->integer('image_type')->comment('画像タイプ 1:メイン 2:サブ');
            $table->string('img_url', 255)->comment('画像url');
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
        Schema::dropIfExists('game_image');
    }
};
