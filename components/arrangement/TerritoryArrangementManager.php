<?php

namespace app\components\arrangement;

use app\facades\ArrangementModelFacade;
use app\helpers\MathHelper;
use app\models\work\AgesWeightChangeableWork;
use app\models\work\AgesWeightWork;
use app\models\work\ArrangementWork;
use app\models\work\ObjectWork;
use app\models\work\PeopleTerritoryWork;
use app\models\work\UserWork;
use yii\db\Exception;

class TerritoryArrangementManager
{
    public TerritoryConcept $territory;

    public function __construct(TerritoryConcept $territory)
    {
        $this->territory = $territory;
    }

    /**
     * @param int $territoryId идентификатор территории
     * @param int $genType тип генерации (базовые веса, измененные веса, личные голоса)
     * @param int $cellsCount количество "ячеек" на площадке
     * @param array $votes необязательный параметр, массив голосов пользователя (пример: [ObjectWork::TYPE_RECREATION => 5, ...])
     */
    public function setTerritoryState($territoryId, $genType, $cellsCount, $votes = [])
    {
        if ($genType == TerritoryConcept::TYPE_SELF_VOTES && count($votes) !== 4) {
            throw new Exception('Некорректно заполнен массив $votes');
        }

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

            if ($genType !== TerritoryConcept::TYPE_SELF_VOTES) {
                $recreationPart += $interval->count * $weights->recreation_weight;
                $sportPart += $interval->count * $weights->sport_weight;
                $educationPart += $interval->count * $weights->education_weight;
                $gamePart += $interval->count * $weights->game_weight;
            }

        }

        if ($genType !== TerritoryConcept::TYPE_SELF_VOTES) {
            $recreationPart = round($recreationPart, 2);
            $sportPart = round($sportPart, 2);
            $educationPart = round($educationPart, 2);
            $gamePart = round($gamePart, 2);
        }
        else {
            $recreationPart = round($votes[ObjectWork::TYPE_RECREATION] / 10, 2);
            $sportPart = round($votes[ObjectWork::TYPE_SPORT] / 10, 2);
            $educationPart = round($votes[ObjectWork::TYPE_EDUCATION] / 10, 2);
            $gamePart = round($votes[ObjectWork::TYPE_GAME] / 10, 2);
        }


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
        try {
            /** @var ObjectWork $object */

            if (!$this->allowedInstall($object, $left, $top, $position)) {
                throw new \Exception('Здесь нельзя строить!');
            }

            switch ($position) {
                case TerritoryConcept::HORIZONTAL_POSITION:
                    $this->territory->installHorizontalObject($object, $left, $top);
                    break;
                case TerritoryConcept::VERTICAL_POSITION:
                    $this->territory->installVerticalObject($object, $left, $top);
                    break;
                default:
                    throw new \Exception('Неизвестный тип позиционирования');
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
        catch (\Exception $e) {
            return;
        }

        $this->territory->state->addToObjectIds($object->id);
        $this->territory->state->addToObjectsList($object, $left, $top, $position);
    }

    public function removeObject($object, $left, $top, $position)
    {
        /** @var ObjectWork $object */
        $object->convertDimensionsToCells(TerritoryConcept::STEP);
        $length = $position == TerritoryConcept::HORIZONTAL_POSITION ? $object->lengthCells : $object->widthCells;
        $width = $position == TerritoryConcept::HORIZONTAL_POSITION ? $object->widthCells : $object->lengthCells;

        for ($i = $top; $i < $top + $width; $i++) {
            for ($j = $left; $j < $left + $length; $j++) {
                //var_dump("[$i,$j] ".$this->territory->matrix[$i][$j]);
                $this->territory->matrix[$i][$j] = 0;
                //var_dump("[$i,$j] ".$this->territory->matrix[$i][$j]);
            }
        }

        // тут надо затирать в матрице объект

        $this->territory->state->deleteObjectById($object->id, $left, $top);
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
        //$maxTop -= 1;

        $maxLeft = $left + ObjectWork::convertDistanceToCells($side1, TerritoryConcept::STEP);
        $maxLeft = $maxLeft + $deadZoneCells < count($this->territory->matrix[0]) ? $maxLeft + $deadZoneCells : count($this->territory->matrix[0]);
        //$maxLeft -= 1;

        if ($left + ObjectWork::convertDistanceToCells($side1, TerritoryConcept::STEP) > $this->territory->lengthCellCount ||
            $top + ObjectWork::convertDistanceToCells($side2, TerritoryConcept::STEP) > $this->territory->widthCellCount) {
            $control = false;
        }
        else {
            for ($i = $newTop; $i < $maxTop; $i++) {
                for ($j = $newLeft; $j < $maxLeft; $j++) {
                    if ($this->territory->matrix[$i][$j] != '0') {
                        $control = false;
                    }
                }
            }
        }

        return $control;
    }

    // подставляет в текущую расстановку подходящий объект
    public function setSuitableObject()
    {
        $fills = [
            ObjectWork::TYPE_RECREATION => $this->territory->state->fillRecreation,
            ObjectWork::TYPE_SPORT => $this->territory->state->fillSport,
            ObjectWork::TYPE_EDUCATION => $this->territory->state->fillEducation,
            ObjectWork::TYPE_GAME => $this->territory->state->fillGame,
        ];

        $this->territory->state->getSortedFillsDesc($fills);

        $installFlag = false;
        foreach ($fills as $key => $fill) {
            $objectTypeText = 'recreation';
            $fillType = $this->territory->state->fillRecreation;
            if ($key == 2) {
                $objectTypeText = 'sport';
                $fillType = $this->territory->state->fillSport;
            }
            if ($key == 3) {
                $objectTypeText = 'education';
                $fillType = $this->territory->state->fillEducation;
            }
            if ($key == 4) {
                $objectTypeText = 'game';
                $fillType = $this->territory->state->fillGame;
            }

            $objects = ObjectWork::find()->where(['object_type_id' => $key])->andWhere(['NOT IN', 'id', array_keys($this->territory->state->objectIds)])->all();
            arsort($this->territory->state->objectIds);
            $objectIdsCopy = $this->territory->state->objectIds;
            //var_dump($objectIdsCopy);
            while (count($objects) == 0 && count($objectIdsCopy) > 0) {
                $objects = ObjectWork::find()->where(['object_type_id' => $key])->andWhere(['NOT IN', 'id', array_keys($objectIdsCopy)])->all();
                array_pop($objectIdsCopy);
            }
            if (!$this->territory->fullnessIntervals[$objectTypeText]->belongToLastInterval($fillType)) {
                foreach ($objects as $object) {
                    $point = $this->findInstallPoint($object);
                    if ($point) {
                        //$installFlag = !($this->installObject($object, $point[0], $point[1], $point[2]) == false);
                        $installFlag = true;
                        $this->installObject($object, $point[0], $point[1], $point[2]);
                        break;
                    }
                }
            }
            if ($installFlag) {
                break;
            }
        }

        // если не получилось подобрать объект
        return $installFlag;
    }

    // передаем тип очередного объекта и проверяем, есть ли возможность разместить хоть 1 такой объект
    public function isFilled()
    {
        $objects = ObjectWork::find()->all();
        $allowedFlag = true;
        foreach ($objects as $object) {
            $object->convertDimensionsToCells(TerritoryConcept::STEP);
            if ($this->findInstallPoint($object)) {
                $allowedFlag = false;
            }
        }

        return $allowedFlag;
    }

    // ищем первую подходящую точку для установки объекта

    /**
     * @param ObjectWork $object
     * @return array|false|int[]|mixed формат [left, top, position]
     */
    private function findInstallPoint(ObjectWork $object)
    {
        $object->convertDimensionsToCells(TerritoryConcept::STEP);
        $square = $object->getSquareCells();
        switch ($object->object_type_id) {
            case ObjectWork::TYPE_RECREATION:
                if ($this->territory->state->fillRecreation + $square > $this->territory->state->recreationPart) {
                    return false;
                }
                break;
            case ObjectWork::TYPE_SPORT:
                if ($this->territory->state->fillSport + $square > $this->territory->state->sportPart) {
                    return false;
                }
                break;
            case ObjectWork::TYPE_EDUCATION:
                if ($this->territory->state->fillEducation + $square > $this->territory->state->educationPart) {
                    return false;
                }
                break;
            case ObjectWork::TYPE_GAME:
                if ($this->territory->state->fillGame + $square > $this->territory->state->gamePart) {
                    return false;
                }
                break;
            default:
                throw new Exception('Неизвестный тип объекта!');
        }

        $allowedFlag = false;
        $point = [];
        for ($i = 0; $i < $this->territory->lengthCellCount; $i++) {
            for ($j = 0; $j < $this->territory->widthCellCount; $j++) {
                if ($this->allowedInstall($object, $i, $j, TerritoryConcept::HORIZONTAL_POSITION)) {
                    $point = count($point) == 0 ? [$i, $j, TerritoryConcept::HORIZONTAL_POSITION] : $point;
                    $allowedFlag = true;
                }
                if ($this->allowedInstall($object, $i, $j, TerritoryConcept::VERTICAL_POSITION)) {
                    $point = count($point) == 0 ? [$i, $j, TerritoryConcept::VERTICAL_POSITION] : $point;
                    $allowedFlag = true;
                }
            }
        }

        return $allowedFlag ? $point : false;
    }

    /**
     * @param ArrangementModelFacade $model модель данных о территории
     * @param string $generateType тип генерации @see TerritoryConcept
     * @param int $territoryId id территории
     * @return void
     */
    public static function saveArrangement(ArrangementModelFacade $model, $generateType, $territoryId)
    {
        $entity = new ArrangementWork();
        $entity->model = serialize($model);
        $entity->user_id = UserWork::getAuthUser()->id;
        $entity->datetime = date('Y.m.d H:i:s');
        $entity->generate_type = $generateType;
        $entity->territory_id = $territoryId;
        $entity->save();
    }
}