<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Kirim pesan WhatsApp via Fonnte.
     *
     * @return array{ok: bool, id?: mixed, reason?: string|null}
     */
    public static function sendMessage(?string $target, ?string $message): array
    {
        $target = self::normalizePhone($target);
        $token  = config('services.fonnte.token');

        if (!$target || !$message) {
            Log::warning('Fonnte: nomor/pesan kosong, pengiriman dibatalkan.');
            return ['ok' => false, 'reason' => 'nomor/pesan kosong'];
        }

        if (!$token) {
            // Penyebab umum: env() ter-cache kosong (php artisan config:cache) atau .env belum diisi.
            Log::error('Fonnte: token tidak terkonfigurasi. Cek FONNTE_TOKEN & jalankan php artisan config:clear.');
            return ['ok' => false, 'reason' => 'token belum dikonfigurasi'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])
                ->asForm()
                ->connectTimeout(5)
                ->timeout(15)
                ->retry(2, 300)
                ->post(rtrim(config('services.fonnte.base_url'), '/') . '/send', [
                    'target'      => $target,
                    'message'     => $message,
                    'countryCode' => '62',
                ]);

            $result = $response->json() ?? [];

            // Fonnte memakai "status" (sukses) tapi kadang "Status" (huruf besar) saat error.
            $status = $result['status'] ?? $result['Status'] ?? null;
            $ok     = $response->ok() && $status === true && !empty($result['id']);

            if (!$ok) {
                Log::warning('Fonnte WA gagal', [
                    'target' => self::mask($target),
                    'http'   => $response->status(),
                    'reason' => $result['reason'] ?? null,
                    'result' => $result,
                ]);
            }

            return [
                'ok'     => $ok,
                'id'     => $result['id'] ?? null,
                'reason' => $result['reason'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Fonnte WA error', [
                'target' => self::mask($target),
                'error'  => $e->getMessage(),
            ]);
            return ['ok' => false, 'reason' => $e->getMessage()];
        }
    }

    /**
     * Normalisasi nomor ke format internasional 62xxxx.
     * Menangani awalan 0, 62, +62, dan 8.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        $p = preg_replace('/[^0-9]/', '', (string) $phone);

        if ($p === '') {
            return null;
        }
        if (str_starts_with($p, '0')) {
            return '62' . substr($p, 1);
        }
        if (str_starts_with($p, '62')) {
            return $p;
        }
        if (str_starts_with($p, '8')) {
            return '62' . $p;
        }

        return $p;
    }

    /**
     * Samarkan nomor untuk keperluan logging (hindari simpan PII penuh).
     */
    private static function mask(string $target): string
    {
        if (strlen($target) <= 7) {
            return '****';
        }

        return substr($target, 0, 4) . '****' . substr($target, -3);
    }
}
