<?php

namespace Spatie\Permission\Exceptions;

use InvalidArgumentException;

class RoleAlreadyExists extends InvalidArgumentException
{
    public static function create(string $roleName, string $guardName)
    {
        return new static("角色： `{$roleName}` 已经存在于路由守卫 `{$guardName}`中.");
    }
}
