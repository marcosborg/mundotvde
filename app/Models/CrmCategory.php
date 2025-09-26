<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmCategory extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'crm_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'slug',
        'color',
        'position',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function stages()
    {
        return $this->hasMany(CrmStage::class, 'category_id');
    }
    public function forms()
    {
        return $this->hasMany(CrmForm::class, 'category_id');
    }
    public function cards()
    {
        return $this->hasMany(CrmCard::class, 'category_id');
    }
}
