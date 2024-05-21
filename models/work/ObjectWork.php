<?php

namespace app\models\work;

use app\components\arrangement\TerritoryConcept;
use app\models\common\ObjectT;

class ObjectWork extends ObjectT
{
    const TYPE_RECREATION = '1';
    const TYPE_SPORT = '2';
    const TYPE_EDUCATION = '3';
    const TYPE_GAME = '4';

    public $lengthCells = 0;
    public $widthCells = 0;

    public static function types()
    {
        return [
            self::TYPE_RECREATION,
            self::TYPE_SPORT,
            self::TYPE_EDUCATION,
            self::TYPE_GAME,
        ];
    }

    public function convertDimensionsToCells($step)
    {
        $this->lengthCells = self::convertDistanceToCells($this->length, TerritoryConcept::STEP);
        $this->widthCells = self::convertDistanceToCells($this->width, TerritoryConcept::STEP);
    }

    // функция конвертации обычного расстояния в "ячейки"
    public static function convertDistanceToCells($distance, $step)
    {
        $cells = intdiv($distance, $step);
        return $distance % $step != 0 ? $cells + 1 : $cells;
    }
}