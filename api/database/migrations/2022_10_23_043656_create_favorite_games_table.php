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
        Schema::create('favorite_games', function (Blueprint $table) {
            $table->id();
            $table->integer('games_id')->comment('お気に入りしたゲームid');
            $table->integer('user_id')->comment('ユーザーid');
            $table->boolean('is_disabled')->default(false)->comment('無効フラグ');
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
        Schema::dropIfExists('favorite_games');
    }
};
