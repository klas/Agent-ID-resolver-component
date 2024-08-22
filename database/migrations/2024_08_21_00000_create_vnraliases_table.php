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
        Schema::create('vnraliases', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            //$table->foreignIdFor(GeselchaftsMaklers::class);
            $table->bigInteger('geselschafts_maklers_id')->unsigned()
                ->index('geselschafts_maklers_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geselchafts_maklers_id');
    }
};
