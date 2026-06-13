<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE document_requests 
            MODIFY COLUMN status ENUM(
                'dalam pengajuan',
                'dalam proses verifikasi',
                'dalam proses pencetakan',
                'sudah tercetak',
                'selesai pengambilan',
                'ditolak'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE document_requests 
            MODIFY COLUMN status ENUM(
                'dalam proses verifikasi',
                'dalam proses pencetakan',
                'sudah tercetak',
                'selesai pengambilan',
                'ditolak'
            ) NOT NULL
        ");
    }
};
