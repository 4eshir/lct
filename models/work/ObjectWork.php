<?php

namespace app\models\work;

use app\models\common\ObjectT;

class ObjectWork extends ObjectT
{
    // функция конвертации обычного расстояния в "ячейки"
    public static function convertDistanceToCells($distance, $step)
    {
        $cells = intdiv($distance, $step);
        return $distance % $step != 0 ? $cells + 1 : $cells;
    }
}