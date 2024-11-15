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
        Schema::table('companies_agents', function (Blueprint $table) {
            $table->foreign(['company_id'], 'company_fk')->references(['id'])
                ->on('companies')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::table('companies_agents', function (Blueprint $table) {
            $table->dropForeign('company_fk');
            $table->dropForeign('agent_fk');
        });
    }
};
