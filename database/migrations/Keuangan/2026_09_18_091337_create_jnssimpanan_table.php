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
        Schema::create('ms_jnssimpanan', function (Blueprint $table) {
            $table->increments('mjsJnsSimpananId');
            $table->string('mjsKodeSimpanan')->nullable();
            $table->string('mjsNamaSimpanan')->nullable();
            $table->string('mjsTipeSimpanan', 50)->nullable();
            $table->decimal('mjsNominalMinimal', 15, 2)->default(0);
            $table->enum('mjsBisaDitarik', ['Y', 'N'])->default('N');
            $table->string('mjsAkunGl', 50)->nullable();
            $table->text('mjsKeterangan')->nullable();
            $table->string('mjsStatus', 20)->default('Active');
            $table->string('mjsCreateBy', 50)->nullable();
            $table->datetime('mjsCreateTime')->nullable();
            $table->string('mjsUpdateBy', 50)->nullable();
            $table->datetime('mjsUpdateTime')->nullable();
            $table->string('mjsDeleteBy', 50)->nullable();
            $table->datetime('mjsDeleteTime')->nullable();

            $table->index(['mjsKodeSimpanan', 'mjsNamaSimpanan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_jnssimpanan');
    }
};
