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
        Schema::create('arus_kas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idKas')->nullable();
            $table->string('jenis_kas');
            $table->string('jenis_transaksi');
            $table->decimal('jumlah',15,2);
            $table->string('keterangan');
            $table->foreign('idKas')->references('id')->on('kas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arus__kas');
    }
};
