<?php

namespace Spatie\Permission\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Show extends Command
{
    protected $signature = 'permission:show
            {guard? : 路由守卫名}
            {style? : 显示样式 (default|borderless|compact|box)}';

    protected $description = '显示每个守卫的角色和权限表';

    public function handle()
    {
        $style = $this->argument('style') ?? 'default';
        $guard = $this->argument('guard');

        if ($guard) {
            $guards = Collection::make([$guard]);
        } else {
            $guards = Permission::pluck('guard_name')->merge(Role::pluck('guard_name'))->unique();
        }

        foreach ($guards as $guard) {
            $this->info("路由守卫: $guard");

            $roles = Role::whereGuardName($guard)->orderBy('name')->get()->mapWithKeys(function (Role $role) {
                return [$role->name => $role->permissions->pluck('name')];
            });

            $permissions = Permission::whereGuardName($guard)->orderBy('name')->pluck('name');

            $body = $permissions->map(function ($permission) use ($roles) {
                return $roles->map(function (Collection $role_permissions) use ($permission) {
                    return $role_permissions->contains($permission) ? ' ✔' : ' ·';
                })->prepend($permission);
            });

            $this->table(
                $roles->keys()->prepend('')->toArray(),
                $body->toArray(),
                $style
            );
        }
    }
}
