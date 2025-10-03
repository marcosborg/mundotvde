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
        // Helper para inputs <input type="date">
        'due_at_html',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'fields_snapshot_json' => 'array',
    ];

    public const PRIORITY_RADIO = [
        'low'    => 'low',
        'medium' => 'medium',
        'high'   => 'high',
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

    protected $dates = [
        'won_at',
        'closed_at',
        'due_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'category_id',
        'stage_id',
        'title',

        // Integração com forms
        'source',                // manual|form|import|api
        'form_id',               // crm_forms.id (opcional)
        'form_submission_id',    // crm_form_submissions.id (opcional, referência direta)
        'fields_snapshot_json',  // JSON dos campos recebidos (opcional)

        'priority',
        'status',
        'lost_reason',
        'won_at',
        'closed_at',
        'due_at',
        'assigned_to_id',
        'created_by_id',
        'position',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /* ---------- Serialização padrão ---------- */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /* ---------- Media Library ---------- */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('crm_card_attachments');
    }

    public function getCrmCardAttachmentsAttribute()
    {
        return $this->getMedia('crm_card_attachments');
    }

    /* ---------- Relações ---------- */
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

    /**
     * NOVA relação principal para submissão do form (quando o card aponta para a submissão).
     */
    public function formSubmission()
    {
        return $this->belongsTo(CrmFormSubmission::class, 'form_submission_id');
    }

    /**
     * Retro-compatibilidade: algumas partes antigas podem procurar a submissão via created_card_id.
     * Mantemos o hasOne antigo para não partir nada.
     */
    public function submission()
    {
        return $this->hasOne(CrmFormSubmission::class, 'created_card_id');
    }

    public function notes()
    {
        return $this->hasMany(CrmCardNote::class, 'card_id');
    }

    public function activities()
    {
        return $this->hasMany(CrmCardActivity::class, 'card_id');
    }

    public function emailsQueue()
    {
        return $this->hasMany(CrmEmailsQueue::class, 'card_id');
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /* ---------- Accessors/Mutators de datas ---------- */
    public function getWonAtAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value)
                ->format(config('panel.date_format').' '.config('panel.time_format'))
            : null;
    }

    public function setWonAtAttribute($value)
    {
        $this->attributes['won_at'] = $this->parseFlexibleDateTime($value);
    }

    public function getClosedAtAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value)
                ->format(config('panel.date_format').' '.config('panel.time_format'))
            : null;
    }

    public function setClosedAtAttribute($value)
    {
        $this->attributes['closed_at'] = $this->parseFlexibleDateTime($value);
    }

    public function getDueAtAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value)
                ->format(config('panel.date_format').' '.config('panel.time_format'))
            : null;
    }

    public function setDueAtAttribute($value)
    {
        // Aceita 'Y-m-d' (do input date), o formato do painel, ou qualquer valor parseável.
        if (!$value) {
            $this->attributes['due_at'] = null;
            return;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $this->attributes['due_at'] = Carbon::createFromFormat('Y-m-d', $value)->startOfDay()->format('Y-m-d H:i:s');
            return;
        }

        $panelFmt = config('panel.date_format').' '.config('panel.time_format');
        try {
            $dt = Carbon::createFromFormat($panelFmt, $value);
            $this->attributes['due_at'] = $dt->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            $this->attributes['due_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
        }
    }

    /**
     * Campo auxiliar para usar diretamente em <input type="date">
     */
    public function getDueAtHtmlAttribute(): ?string
    {
        $raw = $this->getRawOriginal('due_at');
        return $raw ? Carbon::createFromFormat('Y-m-d H:i:s', $raw)->format('Y-m-d') : null;
    }

    private function parseFlexibleDateTime($value): ?string
    {
        if (!$value) return null;

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay()->format('Y-m-d H:i:s');
        }

        $panelFmt = config('panel.date_format').' '.config('panel.time_format');
        try {
            return Carbon::createFromFormat($panelFmt, $value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        }
    }

    /* ---------- Scopes úteis ---------- */
    public function scopeOpen($q)
    {
        return $q->where('status', 'open');
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('position')->orderBy('id');
    }
}
