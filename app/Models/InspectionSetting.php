<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'push_enabled',
        'default_reminder_policy_json',
    ];

    protected $casts = [
        'push_enabled' => 'boolean',
        'default_reminder_policy_json' => 'array',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'push_enabled' => true,
            'default_reminder_policy_json' => [
                'hours_before' => [24, 2],
                'overdue_hours' => [24],
                'escalate_to_admin_after_hours' => 24,
            ],
        ]);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
