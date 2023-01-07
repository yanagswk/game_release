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
            $table->string('genre', 20)->nullable()->comment('ジャンル')->after('hardware');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->myDropColumnIfExists('games', 'genre');
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
