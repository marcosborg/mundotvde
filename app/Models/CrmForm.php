<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmForm extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'crm_forms';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_RADIO = [
        'draft'     => 'draft',
        'published' => 'published',
        'archived'  => 'archived',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'status',
        'confirmation_message',
        'redirect_url',
        'notify_emails',
        'create_card_on_submit',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function category()
    {
        return $this->belongsTo(CrmCategory::class, 'category_id');
    }
}
