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
        Schema::create('ms_objek', function (Blueprint $table) {
            $table->increments('msoId');
            $table->string('msoAkunKode', 20);
            $table->string('msoKelompokKode', 20);
            $table->string('msoJenisKode', 20);
            $table->string('msoKode', 20)->unique();
            $table->string('msoNama', 100);
            $table->string('msoCreatedBy', 50)->nullable();
            $table->datetime('msoCreatedAt')->nullable();
            $table->string('msoUpdatedBy', 50)->nullable();
            $table->datetime('msoUpdatedAt')->nullable();
            $table->string('msoDeletedBy', 50)->nullable();
            $table->datetime('msoDeletedAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_objek');
    }
};
