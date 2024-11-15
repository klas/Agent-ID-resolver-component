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
        Schema::table('gesellschafts_agents', function (Blueprint $table) {
            $table->foreign(['gesellschaft_id'], 'gesellschaft_fk')->references(['id'])
                ->on('gesellschafts')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['agent_id'], 'agent_fk')->references(['id'])
                ->on('agents')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gesellschafts_agents', function (Blueprint $table) {
            $table->dropForeign('gesellschaft_fk');
            $table->dropForeign('agent_fk');
        });
    }
};
