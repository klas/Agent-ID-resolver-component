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
        Schema::create('geselschafts_maklers', function (Blueprint $table) {
            $table->id()->primary();
            //$table->foreignIdFor(Geselschafts::class);
            //$table->foreignIdFor(Maklers::class);
            $table->bigInteger('geselschaft_id')->unsigned()->index('geselschaft_id');
            $table->bigInteger('makler_id')->unsigned()->index('makler_id');

            $table->unique(['geselschaft_id', 'makler_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geselschafts_maklers');
    }
};
