<?php

namespace Spatie\Permission\Exceptions;

use InvalidArgumentException;

class PermissionAlreadyExists extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName)
    {
        return new static("权限名：`{$permissionName}` 已经存在于路由守卫： `{$guardName}`中.");
    }
}
