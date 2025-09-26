<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmStage extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'crm_stages';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'position',
        'color',
        'is_won',
        'is_lost',
        'auto_assign_to_user_id',
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

    public function auto_assign_to_user()
    {
        return $this->belongsTo(User::class, 'auto_assign_to_user_id');
    }
}
