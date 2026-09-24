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
        Schema::create('ms_bankkas', function (Blueprint $table) {
            $table->increments('msbkId');
            $table->string('msbkKode', 50)->nullable();
            $table->string('msbkObjekKd', 20)->nullable();
            $table->string('msbkObjekNm', 200)->nullable();
            $table->string('msbkNoRek', 200)->nullable();
            $table->text('msbkAtasNama')->nullable();
            $table->decimal('msbkSaldoSekarang', 20, 2)->default(0)->comment('Saldo Sekarang');
            $table->string('msbkStatus', 20)->default('Active');
            $table->date('msbkTanggal')->nullable();
            $table->decimal('msbkSaldoAwal', 20, 2)->nullable();

            $table->string('msbkCreatedBy', 50)->nullable();
            $table->timestamp('msbkCreatedTime')->nullable();
            $table->string('msbkUpdatedBy', 50)->nullable();
            $table->timestamp('msbkUpdatedTime')->nullable();
            $table->string('msbkDeletedBy', 50)->nullable();
            $table->timestamp('msbkDeletedTime')->nullable();

            $table->index(['msbkKode', 'msbkObjekKd', 'msbkNoRek']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_bankkas');
    }
};
