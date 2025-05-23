<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'drivers';

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'card_id',
        'operation_id',
        'local_id',
        'start_date',
        'end_date',
        'reason',
        'phone',
        'payment_vat',
        'citizen_card',
        'email',
        'iban',
        'address',
        'zip',
        'city',
        'state_id',
        'driver_license',
        'driver_vat',
        'uber_uuid',
        'bolt_name',
        'license_plate',
        'brand',
        'model',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tvde_operators()
    {
        return $this->belongsToMany(TvdeOperator::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function activity_launches()
    {
        return $this->hasMany(ActivityLaunch::class);
    }

    public function admin_contract()
    {
        return $this->hasOne(AdminContract::class);
    }

    public function driverDocuments()
    {
        return $this->hasMany(Document::class, 'driver_id', 'id');
    }

    public function driverReceipts()
    {
        return $this->hasMany(Receipt::class, 'driver_id', 'id');
    }

    // app/Models/Driver.php

    public function receipts()
    {
        return $this->hasMany(\App\Models\Receipt::class);
    }
}
