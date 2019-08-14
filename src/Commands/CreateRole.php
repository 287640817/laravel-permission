<?php

namespace Spatie\Permission\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class CreateRole extends Command
{
    protected $signature = 'permission:create-role
        {name : 角色名}
        {guard? : 路由守卫名}
        {permissions? : 分配给角色的权限列表，由 | 分隔}';

    protected $description = '创建一个角色';

    public function handle()
    {
        $roleClass = app(RoleContract::class);

        $role = $roleClass::findOrCreate($this->argument('name'), $this->argument('guard'));

        $role->givePermissionTo($this->makePermissions($this->argument('permissions')));

        $this->info("角色 `{$role->name}` 创建成功");
    }

    protected function makePermissions($string = null)
    {
        if (empty($string)) {
            return;
        }

        $permissionClass = app(PermissionContract::class);

        $permissions = explode('|', $string);

        $models = [];

        foreach ($permissions as $permission) {
            $models[] = $permissionClass::findOrCreate(trim($permission), $this->argument('guard'));
        }

        return collect($models);
    }
}
