<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tr_saldoawal', function (Blueprint $table) {
            $table->increments('tsId');
            $table->string('tsKodeBank')->nullable();
            $table->string('tsNamaBank')->nullable();
            $table->decimal('tsSaldoAwal', 15, 2)->default(0);
            $table->string('tsNoTrans')->nullable();
            $table->string('tsTahun')->nullable();
            $table->date('tsTanggal')->nullable();
            $table->string('tsKodeBankKas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_saldoawal');
    }
};
