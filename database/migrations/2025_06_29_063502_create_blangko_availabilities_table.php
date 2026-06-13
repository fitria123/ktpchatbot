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
        Schema::create('blangko_availabilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->time('waktu');
            $table->date('tanggal');
            $table->string('no_bast', 100);
            $table->integer('jumlah_blanko')->unsigned();
            $table->integer('jumlah_total')->unsigned();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blangko_availabilities');
    }
};
