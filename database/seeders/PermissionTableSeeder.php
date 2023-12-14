<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'roles',
            'roles.create',
            'roles.edit',
            'roles.destroy',
            'users',
            'users.create',
            'users.edit',
            'users.destroy',
            'order',
            'reports',
            'new_order',
            'iss.for.des/cam',
            'rec.for.des/cam',
            'qc&iss.for.waxing',
            'req.for.waxing',
            'qc&iss.for.casting',
            'req.for.casting',
            'iss.for.hisab',
            'req.for.hisab',
            'qc&iss.for.del/cen',
            'req.for.del/cen',
            'iss.for.ready',
            'req.for.ready',
            'delivery/complete',
            'task_management',
            'iss.for.delivery',
            'rec.for.delivery',
            'iss.for.packing',
            'rec.for.packing',
            'iss.for.saleing',
         ];

         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
