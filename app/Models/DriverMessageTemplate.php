<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DriverMessageTemplate extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];
    public const TYPES = ['own_vehicle' => 'Viatura própria', 'company_rental' => 'Viatura alugada nossa', 'external_rental' => 'Viatura alugada de terceiros'];
}
