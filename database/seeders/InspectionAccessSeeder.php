<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class InspectionAccessSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            'inspection_access',
            'inspection_create',
            'inspection_edit',
            'inspection_show',
            'inspection_delete',
        ];

        $permissionIds = [];

        foreach ($titles as $title) {
            $permission = Permission::withTrashed()->firstOrNew(['title' => $title]);

            if (!$permission->exists) {
                $permission->save();
            } elseif ($permission->trashed()) {
                $permission->restore();
            }

            $permissionIds[$title] = $permission->id;
        }

        Role::withTrashed()->whereIn('title', ['Admin', 'Gestor'])->get()->each(function (Role $role) use ($permissionIds) {
            if ($role->trashed()) {
                $role->restore();
            }

            $role->permissions()->syncWithoutDetaching(array_values($permissionIds));
        });

        Role::withTrashed()->where('title', 'Motorista')->get()->each(function (Role $role) use ($permissionIds) {
            if ($role->trashed()) {
                $role->restore();
            }

            $role->permissions()->syncWithoutDetaching([
                $permissionIds['inspection_access'],
                $permissionIds['inspection_create'],
                $permissionIds['inspection_edit'],
                $permissionIds['inspection_show'],
            ]);
        });
    }
}
