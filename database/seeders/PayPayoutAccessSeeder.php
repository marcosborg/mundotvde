<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PayPayoutAccessSeeder extends Seeder
{
    public function run()
    {
        $permission = Permission::withTrashed()->firstOrNew([
            'title' => 'pay_payout_access',
        ]);

        if (!$permission->exists) {
            $permission->save();
        } elseif ($permission->trashed()) {
            $permission->restore();
        }

        Role::withTrashed()
            ->whereIn('title', ['Admin', 'Gestor'])
            ->get()
            ->each(function (Role $role) use ($permission) {
                if ($role->trashed()) {
                    $role->restore();
                }

                $role->permissions()->syncWithoutDetaching([$permission->id]);
            });
    }
}
