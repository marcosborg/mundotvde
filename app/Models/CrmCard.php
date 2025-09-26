<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CrmCard extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'crm_cards';

    protected $appends = [
        'crm_card_attachments',
    ];

    public const PRIORITY_RADIO = [
        'low'    => 'low',
        'medium' => 'medium',
        'high'   => 'high',
    ];

    protected $dates = [
        'won_at',
        'closed_at',
        'due_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const SOURCE_RADIO = [
        'form'   => 'form',
        'manual' => 'manual',
        'import' => 'import',
        'api'    => 'api',
    ];

    public const STATUS_RADIO = [
        'open'     => 'open',
        'won'      => 'won',
        'lost'     => 'lost',
        'archived' => 'archived',
    ];

    protected $fillable = [
        'category_id',
        'stage_id',
        'title',
        'form_id',
        'source',
        'priority',
        'status',
        'lost_reason',
        'won_at',
        'closed_at',
        'due_at',
        'assigned_to_id',
        'created_by_id',
        'position',
        'fields_snapshot_json',
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

    public function category()
    {
        return $this->belongsTo(CrmCategory::class, 'category_id');
    }

    public function stage()
    {
        return $this->belongsTo(CrmStage::class, 'stage_id');
    }

    public function form()
    {
        return $this->belongsTo(CrmForm::class, 'form_id');
    }

    public function getWonAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setWonAtAttribute($value)
    {
        $this->attributes['won_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getClosedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setClosedAtAttribute($value)
    {
        $this->attributes['closed_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getDueAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setDueAtAttribute($value)
    {
        $this->attributes['due_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getCrmCardAttachmentsAttribute()
    {
        return $this->getMedia('crm_card_attachments');
    }
}
