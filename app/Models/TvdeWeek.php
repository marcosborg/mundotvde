<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TvdeWeek extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'tvde_weeks';

    protected $dates = [
        'start_date',
        'end_date',
        'locked_at',
        'reopened_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'tvde_month_id',
        'number',
        'status',
        'locked_at',
        'locked_by',
        'reopened_at',
        'reopened_by',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function tvde_month()
    {
        return $this->belongsTo(TvdeMonth::class, 'tvde_month_id');
    }

    public function activityLaunches()
    {
        return $this->hasMany(ActivityLaunch::class, 'week_id');
    }

    public function statementSnapshots()
    {
        return $this->hasMany(WeeklyStatementSnapshot::class, 'tvde_week_id');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function reopenedBy()
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isOpen(): bool
    {
        return !$this->isClosed();
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

}
