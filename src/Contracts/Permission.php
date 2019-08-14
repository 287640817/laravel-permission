<?php

namespace Spatie\Permission\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    /**
     * 权限角色关联表.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany;

    /**
     * 通过权限名查找权限.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return Permission
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * 通过权限ID查找权限.
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return Permission
     */
    public static function findById(int $id, $guardName): self;

    /**
     * 查找创建角色.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return Permission
     */
    public static function findOrCreate(string $name, $guardName): self;
}
