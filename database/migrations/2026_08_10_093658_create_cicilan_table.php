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
        Schema::create('tr_cicilan', function (Blueprint $table) {
            $table->bigIncrements('trcId');
            $table->string('trcNoTrans', 100)->unique();
            $table->integer('trcUserId');
            $table->date('trcTanggal');
            $table->date('trcJatuhTempo');
            $table->decimal('trcNominalPokok', 20, 2);
            $table->decimal('trcPokokBayar', 20, 2);
            $table->integer('trcTenor')->default(0);
            $table->text('trcKeterangan')->nullable();
            $table->integer('trcStatus')->default(1)->comment('1: Aktif, 0: Nonaktif');
            $table->dateTime('trcCreatedAt')->nullable();
            $table->string('trcCreatedBy', 225)->useCurrent()->nullable();
            $table->dateTime('trcUpdatedAt')->nullable();
            $table->string('trcUpdatedBy', 225)->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_cicilan');
    }
};
