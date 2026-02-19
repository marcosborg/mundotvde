<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionDefect extends Model
{
    use HasFactory;

    protected $table = 'inspection_defects';

    protected $fillable = [
        'vehicle_id',
        'created_from_submission_id',
        'title',
        'description',
        'severity',
        'status',
        'created_by_user_id',
        'assigned_to_user_id',
    ];

    public function vehicle()
    {
        return $this->belongsTo(VehicleItem::class, 'vehicle_id');
    }

    public function submission()
    {
        return $this->belongsTo(InspectionSubmission::class, 'created_from_submission_id');
    }

    public function photos()
    {
        return $this->belongsToMany(InspectionPhoto::class, 'inspection_defect_photos', 'defect_id', 'photo_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
