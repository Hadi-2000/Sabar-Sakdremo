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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_arus_kas');
            $table->string('jenis_transaksi');
            $table->string('nama_barang');
            $table->string('jumlah_barang');
            $table->decimal('harga_total', 15, 2);
            $table->foreign('id_pelanggan')->references('id')->on('pelanggans');
            $table->foreign('id_arus_kas')->references('id')->on('arus__kas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
