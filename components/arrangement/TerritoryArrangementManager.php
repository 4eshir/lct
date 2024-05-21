<?php

namespace app\components\arrangement;

use app\helpers\MathHelper;
use app\models\work\AgesWeightChangeableWork;
use app\models\work\AgesWeightWork;
use app\models\work\ObjectWork;
use app\models\work\PeopleTerritoryWork;
use yii\db\Exception;

class TerritoryArrangementManager
{
    public TerritoryConcept $territory;

    public function __construct(TerritoryConcept $territory)
    {
        $this->territory = $territory;
    }

    public function setTerritoryState($territoryId, $genType, $cellsCount)
    {
        $people = PeopleTerritoryWork::find()->where(['territory_id' => $territoryId])->orderBy(['ages_interval_id' => SORT_ASC])->all();
        $recreationPart = 0;
        $sportPart = 0;
        $educationPart = 0;
        $gamePart = 0;
        $weights = [];

        foreach ($people as $interval) {
            /** @var PeopleTerritoryWork $interval */
            switch ($genType) {
                case TerritoryConcept::TYPE_BASE_WEIGHTS:
                    $weights = AgesWeightWork::find()->where(['ages_interval_id' => $interval->ages_interval_id])->one();
                    break;
                case TerritoryConcept::TYPE_CHANGE_WEIGHTS:
                    $weights = AgesWeightChangeableWork::find()->where(['ages_interval_id' => $interval->ages_interval_id])->andWhere(['territory_id' => $territoryId])->one();
                    break;
                case TerritoryConcept::TYPE_SELF_VOTES:
                    // coming soon
                    break;
                default:
                    throw new \DomainException('Неизвестный тип генерации');
            }

            $recreationPart += $interval->count * $weights->recreation_weight;
            $sportPart += $interval->count * $weights->sport_weight;
            $educationPart += $interval->count * $weights->education_weight;
            $gamePart += $interval->count * $weights->game_weight;
        }

        $recreationPart = round($recreationPart, 2);
        $sportPart = round($sportPart, 2);
        $educationPart = round($educationPart, 2);
        $gamePart = round($gamePart, 2);

        $values = MathHelper::rationing([$recreationPart, $sportPart, $educationPart, $gamePart], 1);
        $this->territory->state->fill(
            floor($values[0] * $cellsCount),
            floor($values[1] * $cellsCount),
            floor($values[2] * $cellsCount),
            floor($values[3] * $cellsCount));
    }

    /**
     * @param $object
     * @param $left
     * @param $top
     * @param $position
     */
    public function installObject($object, $left, $top, $position)
    {
        /** @var ObjectWork $object */

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

        $object->convertDimensionsToCells(TerritoryConcept::STEP);
        switch ($object->object_type_id) {
            case ObjectWork::TYPE_RECREATION:
                $this->territory->state->addRecreation($object->lengthCells * $object->widthCells);
                break;
            case ObjectWork::TYPE_SPORT:
                $this->territory->state->addSport($object->lengthCells * $object->widthCells);
                break;
            case ObjectWork::TYPE_EDUCATION:
                $this->territory->state->addEducation($object->lengthCells * $object->widthCells);
                break;
            case ObjectWork::TYPE_GAME:
                $this->territory->state->addGame($object->lengthCells * $object->widthCells);
                break;
            default:
                throw new Exception('Неизвестный тип объекта');
        }
    }

    public function allowedInstall($object, $left, $top, $position)
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
                if ($this->territory->matrix[$i][$j] != '0') {
                    $control = false;
                }
            }
        }

        if ($left + ObjectWork::convertDistanceToCells($object->length, TerritoryConcept::STEP) > $this->territory->lengthCellCount ||
            $top + ObjectWork::convertDistanceToCells($object->width, TerritoryConcept::STEP) > $this->territory->widthCellCount) {
            $control = false;
        }


        return $control;
    }

    // подставляет в текущую расстановку подходящий объект
    public function getSuitableObject(array $objects, array $weights)
    {

    }

    // передаем тип очередного объекта и проверяем, есть ли возможность разместить хоть 1 такой объект
    public function isFilled($anotherObjectType)
    {
        if (!in_array($anotherObjectType, ObjectWork::types())) {
            throw new \DomainException('Неизвестный тип объекта');
        }

        $objects = ObjectWork::find()->where(['object_type_id' => $anotherObjectType])->all();
        $allowedFlag = false;
        foreach ($objects as $object) {
            for ($i = 0; $i < $this->territory->lengthCellCount; $i++) {
                for ($j = 0; $j < $this->territory->widthCellCount; $j++) {
                    if ($this->allowedInstall($object, $i, $j, TerritoryConcept::HORIZONTAL_POSITION) ||
                        $this->allowedInstall($object, $i, $j, TerritoryConcept::VERTICAL_POSITION)) {
                        var_dump($object->id.' - '.$i.' '.$j);
                        $allowedFlag = true;
                    }
                }
            }
        }

        return $allowedFlag;
    }
}