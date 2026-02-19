<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'performer_type',
        'schema_json',
        'required_photo_angles_json',
        'requires_signature',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'schema_json' => 'array',
        'required_photo_angles_json' => 'array',
        'requires_signature' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(InspectionSchedule::class, 'template_id');
    }

    public function assignments()
    {
        return $this->hasMany(InspectionAssignment::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
