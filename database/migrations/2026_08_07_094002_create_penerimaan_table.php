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
        Schema::create('tr_penerimaan', function (Blueprint $table) {
            $table->bigIncrements('trId');
            $table->string('trNoTrans', 100)->nullable();
            $table->date('trTanggal')->nullable();
            $table->string('trJenisTrans', 225)->nullable();
            $table->decimal('trTerimaNominal', 20, 2)->default(0);
            $table->integer('trTerimaUserId')->nullable();
            $table->text('trKeterangan')->nullable();
            $table->dateTime('trCreateTime')->useCurrent();
            $table->string('trCreatedBy', 255)->nullable();
            $table->dateTime('trUpdateTime')->useCurrentOnUpdate()->nullable();
            $table->string('trUpdatedBy', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_penerimaan');
    }
};
