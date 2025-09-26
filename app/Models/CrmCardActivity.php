<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmCardActivity extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'crm_card_activities';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'card_id',
        'type',
        'meta_json',
        'created_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_RADIO = [
        'note'            => 'note',
        'email'           => 'email',
        'stage_change'    => 'stage_change',
        'assignment'      => 'assignment',
        'attachment'      => 'attachment',
        'form_submission' => 'form_submission',
        'custom'          => 'custom',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function card()
    {
        return $this->belongsTo(CrmCard::class, 'card_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
