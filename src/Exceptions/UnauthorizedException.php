<?php

namespace Spatie\Permission\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    private $requiredRoles = [];

    private $requiredPermissions = [];

    public static function forRoles(array $roles): self
    {
        $message = '用户没有相应的角色权限.';

        if (config('permission.display_permission_in_exception')) {
            $permStr = implode(', ', $roles);
            $message = '用户没有相应的角色权限. 您必须属于这些角色： '.$permStr;
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredRoles = $roles;

        return $exception;
    }

    public static function forPermissions(array $permissions): self
    {
        $message = '用户没有相应的权限.';

        if (config('permission.display_permission_in_exception')) {
            $permStr = implode(', ', $permissions);
            $message = '用户没有相应的权限. 您必须有这些权限 '.$permStr;
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $permissions;

        return $exception;
    }

    public static function forRolesOrPermissions(array $rolesOrPermissions): self
    {
        $message = '用户没有访问权限.';

        if (config('permission.display_permission_in_exception') && config('permission.display_role_in_exception')) {
            $permStr = implode(', ', $rolesOrPermissions);
            $message = '用户没有访问权限. 您必须有这些权限： '.$permStr;
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $rolesOrPermissions;

        return $exception;
    }

    public static function notLoggedIn(): self
    {
        return new static(403, '用户未登陆.', null, []);
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}
