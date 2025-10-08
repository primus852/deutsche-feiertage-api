<?php

namespace App\Enum;

enum HolidayRuleType: string
{
    case FIXED = 'FIXED';
    case EASTER = 'EASTER';
    case RELATIVE_TO_EASTER = 'RELATIVE_TO_EASTER';
    case ONE_OFF = 'ONE_OFF';

    public static function fromString($value): self
    {
        return self::from($value);
    }

}
