<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class InspectionPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'inspection_access',
            'inspection_template_access',
            'inspection_schedule_access',
            'inspection_assignment_access',
            'inspection_review_access',
        ];

        foreach ($permissions as $permissionTitle) {
            Permission::firstOrCreate(['title' => $permissionTitle]);
        }

        $admin = Role::find(1);
        if (!$admin) {
            return;
        }

        $permissionIds = Permission::whereIn('title', $permissions)->pluck('id')->all();
        $admin->permissions()->syncWithoutDetaching($permissionIds);
    }
}
