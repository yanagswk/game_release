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
        Schema::table('games', function (Blueprint $table) {
            $table->string('affiliate_url', 255)->nullable()->comment('アフィリエイトurl')->after('item_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->myDropColumnIfExists('games', 'affiliate_url');
    }

    private function myDropColumnIfExists($myTable, $column)
    {
        if (Schema::hasColumn($myTable, $column)) {

            Schema::table($myTable, function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }
};
