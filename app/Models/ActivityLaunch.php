<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLaunch extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'activity_launches';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'driver_id',
        'week_id',
        'rent',
        'management',
        'insurance',
        'fuel',
        'tolls',
        'others',
        'refund',
        'initial_kilometers',
        'final_kilometers',
        'send',
        'paid',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function week()
    {
        return $this->belongsTo(TvdeWeek::class, 'week_id');
    }

    public function activityPerOperators()
    {
        return $this->hasMany(ActivityPerOperator::class);
    }

    public function activityLaunchActivityPerOperators()
    {
        return $this->hasMany(ActivityPerOperator::class, 'activity_launch_id', 'id');
    }

}