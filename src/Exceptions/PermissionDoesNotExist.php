<?php

namespace Spatie\Permission\Exceptions;

use InvalidArgumentException;

class PermissionDoesNotExist extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName = '')
    {
        return new static("在路由守卫中： `{$guardName}`，没有名为 `{$permissionName}`的权限.");
    }

    public static function withId(int $permissionId)
    {
        return new static("没有ID为`{$permissionId}`的权限.");
    }
}
