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
        Schema::create('tr_cicilan_detail', function (Blueprint $table) {
            $table->bigIncrements('trcdId');
            $table->unsignedBigInteger('trcdHeadId');
            $table->foreign('trcdHeadId')->references('trcId')->on('tr_cicilan')->onDelete('cascade');
            $table->date('trcdTanggal');
            $table->decimal('trcdNominal', 20, 2);
            $table->text('trcdKeterangan')->nullable();
            $table->dateTime('trcdCreatedAt')->nullable();
            $table->string('trcdCreatedBy', 225)->useCurrent()->nullable();
            $table->dateTime('trcdUpdatedAt')->nullable();
            $table->string('trcdUpdatedBy', 225)->useCurrentOnUpdate()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_cicilan_detail');
    }
};
