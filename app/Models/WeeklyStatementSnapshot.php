<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyStatementSnapshot extends Model
{
    use HasFactory;

    public $table = 'weekly_statement_snapshots';

    protected $casts = [
        'data_json' => 'array',
        'locked_at' => 'datetime',
    ];

    protected $fillable = [
        'tvde_week_id',
        'activity_launch_id',
        'driver_id',
        'vehicle_id',
        'vehicle_plate',
        'driver_name',
        'data_json',
        'pdf_path',
        'pdf_hash',
        'version',
        'locked_at',
        'locked_by',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function week()
    {
        return $this->belongsTo(TvdeWeek::class, 'tvde_week_id');
    }

    public function launch()
    {
        return $this->belongsTo(ActivityLaunch::class, 'activity_launch_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(VehicleItem::class, 'vehicle_id');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
