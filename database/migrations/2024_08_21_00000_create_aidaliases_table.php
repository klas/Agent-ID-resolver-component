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
        Schema::create('aidaliases', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->bigInteger('gm_id')->unsigned()
                ->index('gm_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aidaliases');
    }
};
