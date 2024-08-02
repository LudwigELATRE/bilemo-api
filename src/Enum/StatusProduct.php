<?php

namespace App\Enum;

class StatusProduct
{
    const AVAILABLE = 'AVAILABLE';
    const OUT_OF_STOCK = 'OUT_OF_STOCK';

    public static function getValues(): array
    {
        return [
            self::AVAILABLE,
            self::OUT_OF_STOCK,
        ];
    }
}
