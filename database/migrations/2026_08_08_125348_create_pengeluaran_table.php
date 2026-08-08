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
        Schema::create('tr_pengeluaran', function (Blueprint $table) {
            $table->bigIncrements('trKelId');
            $table->string('trKelNoTrans', 100)->nullable();
            $table->date('trKelTanggal')->nullable();
            $table->string('trKelJenisTrans', 225)->nullable();
            $table->decimal('trKelNominal', 20, 2)->default(0);
            $table->text('trKelKeterangan')->nullable();
            $table->integer('trKelUserId')->nullable();
            $table->dateTime('trKelCreateTime')->useCurrent();
            $table->string('trKelCreatedBy', 255)->nullable();
            $table->dateTime('trKelUpdateTime')->useCurrentOnUpdate()->nullable();
            $table->string('trKelUpdatedBy', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pengeluaran');
    }
};
