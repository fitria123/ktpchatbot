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
        Schema::create('document_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('antrian_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('antrian_id')->references('id')->on('antrians')->onDelete('set null');

            $table->string('kk');
            $table->string('nik');
            $table->string('desa_kelurahan');
            $table->text('alasan');

            $table->enum('status', [
                'dalam proses verifikasi',
                'dalam proses pencetakan',
                'sudah tercetak',
                'selesai pengambilan',
                'ditolak'
            ])->default('dalam proses verifikasi');
            $table->string('alasan_ditolak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};