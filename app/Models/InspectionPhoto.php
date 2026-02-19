<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'angle',
        'file_disk',
        'file_path',
        'mime',
        'size',
        'sha256',
        'captured_at',
        'uploaded_at',
        'meta_json',
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    protected $dates = [
        'captured_at',
        'uploaded_at',
        'created_at',
        'updated_at',
    ];

    public function submission()
    {
        return $this->belongsTo(InspectionSubmission::class, 'submission_id');
    }

    public function defects()
    {
        return $this->belongsToMany(InspectionDefect::class, 'inspection_defect_photos', 'photo_id', 'defect_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
