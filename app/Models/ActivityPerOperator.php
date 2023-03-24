<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityPerOperator extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'activity_per_operators';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'activity_launch_id',
        'gross',
        'net',
        'taxes',
        'tvde_operator_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function activity_launch()
    {
        return $this->belongsTo(ActivityLaunch::class, 'activity_launch_id');
    }

    public function tvde_operator()
    {
        return $this->belongsTo(TvdeOperator::class, 'tvde_operator_id');
    }
}
