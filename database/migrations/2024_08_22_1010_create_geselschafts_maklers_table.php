<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gesellschafts_agents', function (Blueprint $table) {
            $table->id()->primary();
            //$table->foreignIdFor(Gesellschafts::class);
            //$table->foreignIdFor(Agents::class);
            $table->bigInteger('gesellschaft_id')->unsigned()->index('gesellschaft_id');
            $table->bigInteger('agent_id')->unsigned()->index('agent_id');

            $table->unique(['gesellschaft_id', 'agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gesellschafts_agents');
    }
};
