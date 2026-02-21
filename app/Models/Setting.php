<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk Pengaturan Aplikasi
 *
 * Menyimpan berbagai konfigurasi aplikasi dalam format key-value JSON
 * Contoh:
 * - school_name: Nama sekolah
 * - school_address: Alamat sekolah
 * - school_phone: Nomor telepon sekolah
 * - tracer_frequency: Berapa kali per tahun alumni bisa isi tracer
 * - tracer_month: Bulan apa dimulai periode pengisian tracer
 * - dll
 */
class Setting extends Model
{
    // Nama tabel di database
    protected $table = 'settings';

    /**
     * Field yang dapat diisi secara massal (mass assignment)
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Cast tipe data untuk field tertentu
     */
    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Ambil nilai setting berdasarkan key
     *
     * @param  string  $key  Kunci setting
     * @param  mixed  $default  Nilai default jika key tidak ditemukan
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Set/update nilai setting
     *
     * @param  string  $key  Kunci setting
     * @param  mixed  $value  Nilai yang akan disimpan
     */
    public static function set(string $key, $value): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Hapus setting berdasarkan key
     *
     * @param  string  $key  Kunci setting
     */
    public static function forget(string $key): bool
    {
        return self::where('key', $key)->delete() > 0;
    }
}
