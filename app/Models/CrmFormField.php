<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmFormField extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'crm_form_fields';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_RADIO = [
        'text'     => 'text',
        'textarea' => 'textarea',
        'number'   => 'number',
        'checkbox' => 'checkbox',
        'select'   => 'select',
    ];

    protected $fillable = [
        'form_id',
        'label',
        'type',
        'required',
        'help_text',
        'placeholder',
        'default_value',
        'is_unique',
        'min_value',
        'max_value',
        'options_json',
        'position',
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
}
