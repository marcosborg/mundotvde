<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeLog extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'time_logs';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'driver_id',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'start'    => 'Início',
        'pause'    => 'Pausa',
        'continue' => 'Continuação',
        'end'      => 'Fim',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
