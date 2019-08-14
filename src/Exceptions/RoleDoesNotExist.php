<?php

namespace Spatie\Permission\Exceptions;

use InvalidArgumentException;

class RoleDoesNotExist extends InvalidArgumentException
{
    public static function named(string $roleName)
    {
        return new static("角色`{$roleName}`不存在.");
    }

    public static function withId(int $roleId)
    {
        return new static("没有角色ID`{$roleId}`.");
    }
}
