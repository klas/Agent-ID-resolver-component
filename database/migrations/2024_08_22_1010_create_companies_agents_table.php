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
        Schema::create('companies_agents', function (Blueprint $table) {
            $table->id()->primary();
            //$table->foreignIdFor(Companies::class);
            //$table->foreignIdFor(Agents::class);
            $table->bigInteger('company_id')->unsigned()->index('company_id');
            $table->bigInteger('agent_id')->unsigned()->index('agent_id');

            $table->unique(['company_id', 'agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_agents');
    }
};
