<?php

namespace app\enums;

abstract class StateOrderEnum
{
    const PENDING = 1;
    const CANCELED = 2;
    const PAYED = 3;
    const PROCESSING = 4;

    public static $labels = [
        self::PENDING => 'Pendiente',
        self::PAYED => 'Pagado',
        self::CANCELED => 'Cancelado',
        self::PROCESSING => 'Procesando',

    ];

    public static function getLabel($value)
    {
        return isset(static::$labels[$value]) ? static::$labels[$value] : '';
    }
}