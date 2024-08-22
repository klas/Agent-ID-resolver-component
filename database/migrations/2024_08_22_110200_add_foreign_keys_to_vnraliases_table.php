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
        Schema::table('vnraliases', function (Blueprint $table) {
            $table->foreign(['geselschafts_maklers_id'], 'geselschafts_maklers_fk')->references(['id'])
                ->on('geselschafts_maklers')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vnraliases', function (Blueprint $table) {
            $table->dropForeign('geselchafts_maklers_id');
        });
    }
};