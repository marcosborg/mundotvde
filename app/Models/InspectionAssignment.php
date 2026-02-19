<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'template_id',
        'assigned_user_id',
        'period_start',
        'period_end',
        'due_at',
        'grace_hours',
        'reminder_policy_json',
        'status',
        'generated_by',
    ];

    protected $dates = [
        'period_start',
        'period_end',
        'due_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'reminder_policy_json' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(VehicleItem::class, 'vehicle_id');
    }

    public function template()
    {
        return $this->belongsTo(InspectionTemplate::class, 'template_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function submission()
    {
        return $this->hasOne(InspectionSubmission::class, 'assignment_id');
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, ['pending', 'in_progress', 'rejected'], true) && Carbon::now()->gt($this->due_at);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
