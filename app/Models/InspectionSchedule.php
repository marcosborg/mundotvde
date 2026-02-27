<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionSchedule extends Model
{
    use HasFactory;

    public $table = 'inspection_schedules';

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'frequency_days',
        'start_at',
        'next_run_at',
        'last_run_at',
        'is_active',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function vehicle()
    {
        return $this->belongsTo(VehicleItem::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
