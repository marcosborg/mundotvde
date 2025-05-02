<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappMessage extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'whatsapp_messages';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user',
        'messages',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'messages' => 'array', // ✅ agora o Laravel vai fazer json_decode automático ao ler
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
