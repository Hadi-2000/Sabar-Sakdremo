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
        Schema::create('utang_piutangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pelanggan');
            $table->date('tanggal');
            $table->string('keterangan');
            $table->string('jenis');
            $table->decimal('nominal', 15, 2);
            $table->string('status');
            $table->foreign('id_pelanggan')->references('id')->on('pelanggans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utang_piutangs');
    }
};
