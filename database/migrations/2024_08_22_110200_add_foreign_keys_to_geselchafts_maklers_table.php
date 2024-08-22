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
        Schema::table('geselschafts_maklers', function (Blueprint $table) {
            $table->foreign(['geselschaft_id'], 'geselschaft_fk')->references(['id'])
                ->on('geselschafts')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['makler_id'], 'makler_fk')->references(['id'])
                ->on('maklers')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geselchafts_maklers', function (Blueprint $table) {
            $table->dropForeign('geselchaft_id');
            $table->dropForeign('makler_id');
        });
    }
};
