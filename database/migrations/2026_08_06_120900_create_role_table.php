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
        Schema::create('role', function (Blueprint $table) {
            $table->bigIncrements('rId');
            $table->string('rNama', 225);
            $table->dateTime('rCreatedAt')->useCurrent();
            $table->string('rCreatedBy', 225)->nullable();
            $table->dateTime('rUpdatedAt')->useCurrentOnUpdate()->nullable();
            $table->string('rUpdatedBy', 225)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
