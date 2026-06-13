<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentRequest extends Model
{
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'antrian_id',
        'kk',
        'nik',
        'desa_kelurahan',
        'alasan',
        'status',
        'alasan_ditolak'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function upload()
    {
        return $this->hasOne(Uploads::class, 'request_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class, 'request_id');
    }

    public function antrian()
    {
        return $this->belongsTo(Antrian::class, 'antrian_id');
    }
    public function take()
    {
        return $this->hasOne(TakeEktp::class, 'request_id', 'id');
    }

    public function takeEktp()
    {
        return $this->hasOne(TakeEktp::class, 'request_id');
    }
    
}
