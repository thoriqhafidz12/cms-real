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
        Schema::create('tr_pembayaranpinjaman', function (Blueprint $table) {
            $table->increments('tppId');
            // $table->integer('tppAngsuranId');
            $table->integer('tppPinjamanId');
            $table->integer('tppAnggotaId');
            $table->integer('tppMetodeBayarId');
            $table->string('tppNoBukti', 225);
            $table->date('tppTanggalBayar');
            $table->decimal('tppNominalBayar', 15, 2)->default(0);
            $table->decimal('tppBayarPokok', 15, 2)->default(0);
            $table->decimal('tppBayarBunga', 15, 2)->default(0);
            $table->decimal('tppBayarDenda', 15, 2)->default(0);
            $table->string('tppKeterangan', 255)->nullable();
            $table->string('tppStatus', 50)->default('0');
            $table->string('tppCreatedBy', 50)->nullable();
            $table->datetime('tppCreatedAt')->nullable();
            $table->string('tppUpdatedBy', 50)->nullable();
            $table->datetime('tppUpdatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pembayaranpinjaman');
    }
};
