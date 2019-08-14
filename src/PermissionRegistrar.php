<?php

namespace Spatie\Permission;

use Illuminate\Cache\CacheManager;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Role;
use Illuminate\Contracts\Auth\Access\Gate;
use Spatie\Permission\Contracts\Permission;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class PermissionRegistrar
{
    /** @var \Illuminate\Contracts\Auth\Access\Gate */
    protected $gate;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var \Illuminate\Cache\CacheManager */
    protected $cacheManager;

    /** @var string */
    protected $permissionClass;

    /** @var string */
    protected $roleClass;

    /** @var \Illuminate\Support\Collection */
    protected $permissions;

    /** @var DateInterval|int */
    public static $cacheExpirationTime;

    /** @var string */
    public static $cacheKey;

    /** @var string */
    public static $cacheModelKey;

    /**
     * PermissionRegistrar constructor.
     *
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     * @param \Illuminate\Cache\CacheManager $cacheManager
     */
    public function __construct(Gate $gate, CacheManager $cacheManager)
    {
        $this->gate = $gate;
        $this->permissionClass = config('permission.models.permission');
        $this->roleClass = config('permission.models.role');

        $this->cacheManager = $cacheManager;
        $this->initializeCache();
    }

    protected function initializeCache()
    {
        self::$cacheExpirationTime = config('permission.cache.expiration_time', config('permission.cache_expiration_time'));

        if (app()->version() <= '5.5') {
            if (self::$cacheExpirationTime instanceof \DateInterval) {
                $interval = self::$cacheExpirationTime;
                self::$cacheExpirationTime = $interval->m * 30 * 60 * 24 + $interval->d * 60 * 24 + $interval->h * 60 + $interval->i;
            }
        }

        self::$cacheKey = config('permission.cache.key');
        self::$cacheModelKey = config('permission.cache.model_key');

        $this->cache = $this->getCacheStoreFromConfig();
    }

    protected function getCacheStoreFromConfig(): \Illuminate\Contracts\Cache\Repository
    {
        // the 'default' fallback here is from the permission.php config file, where 'default' means to use config(cache.default)
        $cacheDriver = config('permission.cache.store', 'default');

        // when 'default' is specified, no action is required since we already have the default instance
        if ($cacheDriver === 'default') {
            return $this->cacheManager->store();
        }

        // if an undefined cache store is specified, fallback to 'array' which is Laravel's closest equiv to 'none'
        if (! \array_key_exists($cacheDriver, config('cache.stores'))) {
            $cacheDriver = 'array';
        }

        return $this->cacheManager->store($cacheDriver);
    }

    /**
     * 在gate上注册权限检查方法.
     *
     * @return bool
     */
    public function registerPermissions(): bool
    {
        $this->gate->before(function (Authorizable $user, string $ability) {
            try {
                if (method_exists($user, 'hasPermissionTo')) {
                    return $user->hasPermissionTo($ability) ?: null;
                }
            } catch (PermissionDoesNotExist $e) {
            }
        });

        return true;
    }

    /**
     * 刷新缓存.
     */
    public function forgetCachedPermissions()
    {
        $this->permissions = null;

        return $this->cache->forget(self::$cacheKey);
    }

    /**
     * 根据传递的参数获取权限.
     *
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions(array $params = []): Collection
    {
        if ($this->permissions === null) {
            $this->permissions = $this->cache->remember(self::$cacheKey, self::$cacheExpirationTime, function () {
                return $this->getPermissionClass()
                    ->with('roles')
                    ->get();
            });
        }

        $permissions = clone $this->permissions;

        foreach ($params as $attr => $value) {
            $permissions = $permissions->where($attr, $value);
        }

        return $permissions;
    }

    /**
     * 获取权限类的一个实例.
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    public function getPermissionClass(): Permission
    {
        return app($this->permissionClass);
    }

    public function setPermissionClass($permissionClass)
    {
        $this->permissionClass = $permissionClass;

        return $this;
    }

    /**
     * 获取角色类的一个实例.
     *
     * @return \Spatie\Permission\Contracts\Role
     */
    public function getRoleClass(): Role
    {
        return app($this->roleClass);
    }

    /**
     * 获取缓存类的实例.
     *
     * @return \Illuminate\Contracts\Cache\Store
     */
    public function getCacheStore(): \Illuminate\Contracts\Cache\Store
    {
        return $this->cache->getStore();
    }
}
