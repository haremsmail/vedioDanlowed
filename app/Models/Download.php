<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'title',
        'format_id',
        'quality',
        'status',
        'file_path',
        'error_message',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_DOWNLOADING = 'downloading';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
}
