<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Receipt extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'receipts';

    protected $appends = [
        'file',
    ];

    public const COMPANY_RADIO = [
        'OC'  => 'OC',
        'TGA' => 'TGA',
    ];

    public const IVA_RADIO = [
        'Sem IVA' => 'Sem IVA',
        'IVA 6%'  => 'IVA 6%',
        'IVA 23%' => 'IVA 23%',
    ];

    public const RETENTION_RADIO = [
        'Sem retenção' => 'Sem retenção',
        '11.50%' => '11.50%',
        '23%' => '23%',
        '20%'    => '20%',
        '25%'    => '25%',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'driver_id',
        'value',
        'paid',
        'company',
        'iva',
        'retention',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function getFileAttribute()
    {
        return $this->getMedia('file')->last();
    }
}