<?php

namespace app\enums;

abstract class StateEnum
{
    const ACTIVE = 1;
    const PENDING = 2;
    const DISABLED = 3;

    public static $labels = [
        self::ACTIVE => 'ACTIVO',
        self::PENDING => 'PENDING',
        self::DISABLED => 'DESACTIVADO',


    ];

    public static function getLabel($value)
    {
        return isset(static::$labels[$value]) ? static::$labels[$value] : '';
    }
}