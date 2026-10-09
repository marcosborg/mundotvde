<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DriverContractVersion extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['snapshot' => 'array', 'accepted_at' => 'datetime', 'superseded_at' => 'datetime'];
    public function scopeCurrent($query)
    {
        return $query->whereNull('superseded_at');
    }
    public static function pendingFor($user)
    {
        if (!$user || !config('driver_onboarding.enabled')) {
            return null;
        }
        return static::current()->whereNull('accepted_at')->where('user_id', $user->id)->whereIn('driver_id', Driver::where('user_id', $user->id)->select('id'))->orderBy('id')->first();
    }
}
