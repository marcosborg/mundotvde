<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'template_id',
        'frequency_days',
        'due_time',
        'grace_hours',
        'reminder_policy_json',
        'is_active',
    ];

    protected $casts = [
        'reminder_policy_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(VehicleItem::class, 'vehicle_id');
    }

    public function template()
    {
        return $this->belongsTo(InspectionTemplate::class, 'template_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
