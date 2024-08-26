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
        Schema::create('gesellschafts_maklers', function (Blueprint $table) {
            $table->id()->primary();
            //$table->foreignIdFor(Gesellschafts::class);
            //$table->foreignIdFor(Maklers::class);
            $table->bigInteger('gesellschaft_id')->unsigned()->index('gesellschaft_id');
            $table->bigInteger('makler_id')->unsigned()->index('makler_id');

            $table->unique(['gesellschaft_id', 'makler_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gesellschafts_maklers');
    }
};
