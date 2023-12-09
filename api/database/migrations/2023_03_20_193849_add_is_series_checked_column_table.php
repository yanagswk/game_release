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
        Schema::table('books_item', function (Blueprint $table) {
            $table->tinyInteger('is_series_checked')
                ->default(0)
                ->comment('シリーズチェック')
                ->after('amazon_affiliate_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->myDropColumnIfExists('books_item', 'is_series_checked');
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
