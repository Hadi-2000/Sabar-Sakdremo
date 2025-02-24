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
        Schema::create('perbaikans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mesin');
            $table->string('teknisi');
            $table->string('keterangan');
            $table->decimal('biaya',15,2);
            $table->enum('status', ["Sedang Dikerjakan", "Selesai", "Dibatalkan"]);
            $table->date('tanggal_perbaikan');
            $table->foreign('id_mesin')->references('id')->on('mesins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikans');
    }
};
