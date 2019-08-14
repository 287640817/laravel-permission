<?php

namespace Spatie\Permission;

use Illuminate\Support\Collection;

class Guard
{
    /**
     * 返回（guard name）属性的集合（如果类或对象上存在）
     *否则将返回config/auth.php中存在的保护名称集合。
     * return collection of (guard_name) property if exist on class or object
     * otherwise will return collection of guards names that exists in config/auth.php.
     * @param $model
     * @return Collection
     */
    public static function getNames($model) : Collection
    {
        if (is_object($model)) {
            $guardName = $model->guard_name ?? null;
        }

        if (! isset($guardName)) {
            $class = is_object($model) ? get_class($model) : $model;

            $guardName = (new \ReflectionClass($class))->getDefaultProperties()['guard_name'] ?? null;
        }

        if ($guardName) {
            return collect($guardName);
        }

        return collect(config('auth.guards'))
            ->map(function ($guard) {
                if (! isset($guard['provider'])) {
                    return;
                }

                return config("auth.providers.{$guard['provider']}.model");
            })
            ->filter(function ($model) use ($class) {
                return $class === $model;
            })
            ->keys();
    }

    public static function getDefaultName($class): string
    {
        $default = config('auth.defaults.guard');

        return static::getNames($class)->first() ?: $default;
    }
}
