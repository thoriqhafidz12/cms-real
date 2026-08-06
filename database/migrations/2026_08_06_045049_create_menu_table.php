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
        Schema::create('menu', function (Blueprint $table) {
            $table->bigIncrements('mId');
            $table->string('mNama');
            $table->string('mRoute')->nullable();
            $table->unsignedBigInteger('mParentId')->nullable();
            $table->string('mIcon')->nullable();
            $table->integer('mOrder')->default(0);
            $table->integer('mIsActive')->default(1)->comment('1=active, 0=inactive');
            $table->dateTime('mCreatedAt')->useCurrent();
            $table->string('mCreatedBy')->nullable();
            $table->dateTime('mUpdatedAt')->useCurrentOnUpdate()->nullable();
            $table->string('mUpdatedBy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
