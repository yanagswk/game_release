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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('nickname', 10)->nullable()->comment('ニックネーム')->after('user_id');
            $table->string('email', 50)->nullable()->comment('メールアドレス')->after('nickname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->myDropColumnIfExists('contacts', 'nickname');
        $this->myDropColumnIfExists('contacts', 'email');
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
