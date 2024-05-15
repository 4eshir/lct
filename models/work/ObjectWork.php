<?php

namespace app\models\work;

use app\models\common\ObjectT;

class ObjectWork extends ObjectT
{
    const TYPE_RECREATION = '1';
    const TYPE_SPORT = '2';
    const TYPE_EDUCATIONAL = '3';
    const TYPE_GAME = '4';

    public static function types()
    {
        return [
            self::TYPE_RECREATION,
            self::TYPE_SPORT,
            self::TYPE_EDUCATIONAL,
            self::TYPE_GAME,
        ];
    }

    // функция конвертации обычного расстояния в "ячейки"
    public static function convertDistanceToCells($distance, $step)
    {
        $cells = intdiv($distance, $step);
        return $distance % $step != 0 ? $cells + 1 : $cells;
    }
}