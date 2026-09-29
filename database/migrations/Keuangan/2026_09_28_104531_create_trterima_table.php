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
        Schema::create('tr_terima', function (Blueprint $table) {
            $table->increments('tId');
            $table->string('tSumber', 100)->nullable();
            $table->integer('tSumberId')->nullable();
            $table->string('tInvoice', 100)->nullable();
            $table->string('tKwitansi', 100)->nullable();
            $table->string('tNoPenerimaan', 100)->nullable();
            $table->decimal('tNilaiBayar', 20, 2)->nullable();
            $table->date('tTglBayar')->nullable();
            $table->string('tAsalPenerimaan', 100)->nullable();
            $table->string('tDeskripsi')->nullable();
            $table->string('tCoa', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_terima');
    }
};
