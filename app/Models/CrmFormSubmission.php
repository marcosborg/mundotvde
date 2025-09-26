<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmFormSubmission extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'crm_form_submissions';

    protected $dates = [
        'submitted_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'form_id',
        'category_id',
        'submitted_at',
        'user_agent',
        'referer',
        'utm_json',
        'data_json',
        'created_card_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function form()
    {
        return $this->belongsTo(CrmForm::class, 'form_id');
    }

    public function category()
    {
        return $this->belongsTo(CrmCategory::class, 'category_id');
    }

    public function getSubmittedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setSubmittedAtAttribute($value)
    {
        $this->attributes['submitted_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function created_card()
    {
        return $this->belongsTo(CrmCard::class, 'created_card_id');
    }
}
