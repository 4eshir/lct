<?php

namespace app\components\arrangement;

use app\models\work\ObjectWork;
use yii\db\Exception;

class TerritoryArrangementManager
{
    public TerritoryConcept $territory;

    public function __construct(TerritoryConcept $territory)
    {
        $this->territory = $territory;
    }

    public function installObject(ObjectWork $object, $left, $top, $position)
    {
        if (!$this->allowedInstall($object, $left, $top, $position)) {
            throw new \DomainException('Здесь нельзя строить!');
        }

        switch ($position) {
            case TerritoryConcept::HORIZONTAL_POSITION:
                $this->territory->installHorizontalObject($object, $left, $top);
                break;
            case TerritoryConcept::VERTICAL_POSITION:
                $this->territory->installVerticalObject($object, $left, $top);
                break;
            default:
                throw new \DomainException('Неизвестный тип позиционирования');
        }
    }

    public function allowedInstall(ObjectWork $object, $left, $top, $position)
    {
        $control = true;
        $side1 = $position == TerritoryConcept::HORIZONTAL_POSITION ? $object->length : $object->width;
        $side2 = $position == TerritoryConcept::HORIZONTAL_POSITION ? $object->width : $object->length;
        $deadZoneCells = ObjectWork::convertDistanceToCells($object->dead_zone_size, TerritoryConcept::STEP);

        $newTop = $top - $deadZoneCells >= 0 ? $top - $deadZoneCells : 0;
        $newLeft = $left - $deadZoneCells >= 0 ? $left - $deadZoneCells : 0;

        $maxTop = $top + ObjectWork::convertDistanceToCells($side2,TerritoryConcept::STEP);
        $maxTop = $maxTop + $deadZoneCells < count($this->territory->matrix) ? $maxTop + $deadZoneCells : count($this->territory->matrix);
        $maxTop -= 1;

        $maxLeft = $left + ObjectWork::convertDistanceToCells($side1, TerritoryConcept::STEP);
        $maxLeft = $maxLeft + $deadZoneCells < count($this->territory->matrix[0]) ? $maxLeft + $deadZoneCells : count($this->territory->matrix[0]);
        $maxLeft -= 1;

        for ($i = $newTop; $i < $maxTop; $i++) {
            for ($j = $newLeft; $j < $maxLeft; $j++) {
                if ($this->territory->matrix[$i][$j] != '00') {
                    $control = false;
                }
            }
        }

        return $control;
    }
}