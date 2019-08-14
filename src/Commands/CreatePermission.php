<?php

namespace Spatie\Permission\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class CreatePermission extends Command
{
    protected $signature = 'permission:create-permission 
                {name : 权限名} 
                {guard? : 路由守卫名}';

    protected $description = '创建一个权限';

    public function handle()
    {
        $permissionClass = app(PermissionContract::class);

        $permission = $permissionClass::findOrCreate($this->argument('name'), $this->argument('guard'));

        $this->info("权限 `{$permission->name}` 创建成功");
    }
}
