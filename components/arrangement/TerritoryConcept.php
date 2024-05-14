<?php


namespace app\components\arrangement;


use app\models\work\ObjectWork;

class TerritoryConcept
{
    const HORIZONTAL_POSITION = 0;
    const VERTICAL_POSITION = 1;
    const STEP = 10; // размер "ячейки" в сантиметрах

    public $lengthCellCount; // количество ячеек в длину

    public $widthCellCount; // количество ячеек в ширину

    public $matrix = []; // матрица размещения объектов

    public static function make($length, $width, $step)
    {
        $entity = new static();
        $entity->lengthCellCount = intdiv($length, $step);
        $entity->widthCellCount = intdiv($width, $step);
        $entity->matrix = array_fill(0, $entity->lengthCellCount, array_fill('00', $entity->lengthCellCount, '00'));

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


    public function getSuitableObject(array $objects, array $weights)
    {

    }

    public function isFilled()
    {
        return false;
    }
}