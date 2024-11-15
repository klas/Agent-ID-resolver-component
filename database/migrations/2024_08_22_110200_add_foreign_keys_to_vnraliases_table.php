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
        Schema::table('aidaliases', function (Blueprint $table) {
            $table->foreign(['gm_id'], 'gm_fk')->references(['id'])
                ->on('gesellschafts_agents')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aidaliases', function (Blueprint $table) {
            $table->dropForeign('gm_fk');
        });
    }
};
