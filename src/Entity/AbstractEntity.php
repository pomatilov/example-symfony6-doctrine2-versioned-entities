<?php

namespace App\Entity;

abstract class AbstractEntity
{
    public const MAX_DATE = '31.12.2999 23:59:59';
    public const MAX_DATE_FORMAT = 'd.m.Y H:i:s';

    public static function getMaxPossibleDate()
    {
        return \DateTime::createFromFormat(self::MAX_DATE_FORMAT, self::MAX_DATE);
    }
}
