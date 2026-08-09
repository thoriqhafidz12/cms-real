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
        Schema::create('rpt_perfaktual', function (Blueprint $table) {
            $table->bigIncrements('rpId');
            $table->integer('rpHeadId')->nullable();
            $table->date('rpTanggal')->nullable();
            $table->decimal('rpTerimaNominal', 20, 2)->default(0)->nullable();
            $table->decimal('rpKeluarNominal', 20, 2)->default(0)->nullable();
            $table->integer('rpJenisTrans')->comment('1=Penerimaan, 2=Pengeluaran');
            $table->text('rpKeterangan')->nullable();
            $table->integer('rpUserId');
            $table->string('rpCreatedBy', 200)->nullable();
            $table->dateTime('rpCreatedAt')->useCurrent()->nullable();
            $table->string('rpUpdatedBy', 200)->nullable();
            $table->dateTime('rpUpdatedAt')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpt_perfaktual');
    }
};
