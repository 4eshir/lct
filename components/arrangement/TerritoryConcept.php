<?php


namespace app\components\arrangement;


use app\models\work\ObjectWork;
use Yii;

class TerritoryConcept
{
    /**
     * Типы генерации пространства
     * base - генерация на основе базовых весов
     * change - генерация на основе измененных весов для территории
     * self - генерация на основе голосования человека
     */
    const TYPE_BASE_WEIGHTS = 'base';
    const TYPE_CHANGE_WEIGHTS = 'change';
    const TYPE_SELF_VOTES = 'self';

    const HORIZONTAL_POSITION = 0;
    const VERTICAL_POSITION = 1;
    const STEP = 10; // размер "ячейки" в сантиметрах

    public $lengthCellCount; // количество ячеек в длину

    public $widthCellCount; // количество ячеек в ширину

    public $matrix = []; // матрица размещения объектов

    public TerritoryState $state; //данные по территории

    public function __construct(TerritoryState $state)
    {
        $this->state = $state;
    }

    public static function make($length, $width, $step)
    {
        $entity = Yii::createObject(self::class);
        $entity->lengthCellCount = intdiv($length, $step);
        $entity->widthCellCount = intdiv($width, $step);
        $entity->matrix = array_fill(0, $entity->widthCellCount, array_fill('0', $entity->lengthCellCount, '0'));

        return $entity;
    }

    // left и top это позиционирование, аналогичное абсолютному в HTML (уже с шагом, т.к. берем из JS)
    public function installHorizontalObject(ObjectWork $object, $left, $top)
    {
        for ($i = $top; $i < $top + ObjectWork::convertDistanceToCells($object->width,self::STEP); $i++) {
            for ($j = $left; $j < $left + ObjectWork::convertDistanceToCells($object->length, self::STEP); $j++) {
                $this->matrix[$i][$j] = (string)$object->id;
            }
        }
    }

    // left и top это позиционирование, аналогичное абсолютному в HTML (уже с шагом, т.к. берем из JS)
    public function installVerticalObject(ObjectWork $object, $left, $top)
    {
        for ($i = $top; $i < $top + ObjectWork::convertDistanceToCells($object->length,self::STEP); $i++) {
            for ($j = $left; $j < $left + ObjectWork::convertDistanceToCells($object->width, self::STEP); $j++) {
                $this->matrix[$i][$j] = (string)$object->id;
            }
        }
    }

    public function getState()
    {
        return $this->state;
    }

    public function showDebugMatrix($stream)
    {
        for ($i = 0; $i < count($this->matrix); $i++) {
            for ($j = 0; $j < count($this->matrix[$i]); $j++) {
                fwrite($stream, $this->matrix[$i][$j].' ');
            }
            fwrite($stream, "\n");
        }

        fwrite($stream, "\n");
    }
}