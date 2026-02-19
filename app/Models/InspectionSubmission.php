<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'started_at',
        'submitted_at',
        'summary_notes',
        'signature_path',
        'location_json',
        'created_by_user_id',
    ];

    protected $casts = [
        'location_json' => 'array',
    ];

    protected $dates = [
        'started_at',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    public function assignment()
    {
        return $this->belongsTo(InspectionAssignment::class, 'assignment_id');
    }

    public function photos()
    {
        return $this->hasMany(InspectionPhoto::class, 'submission_id');
    }

    public function defects()
    {
        return $this->hasMany(InspectionDefect::class, 'created_from_submission_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
