<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentWarning extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'document_warnings';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'citizen_card',
        'tvde_driver_certificate',
        'criminal_record',
        'profile_picture',
        'driving_license',
        'iban',
        'dua_vehicle',
        'car_insurance',
        'ipo_vehicle',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
