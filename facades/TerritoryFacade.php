<?php

namespace app\facades;

use app\components\arrangement\TerritoryArrangementManager;
use app\components\arrangement\TerritoryConcept;
use app\models\work\ObjectWork;

class TerritoryFacade
{
    public ArrangementModelFacade $model;
    public TerritoryArrangementManager $manager;

    public function __construct(TerritoryArrangementManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Генерация расстановки объектов на территории
     * @param string $generateType тип генерации из списка констант @see TerritoryConcept
     * @param array $votes необязательный параметр, массив голосов пользователя (пример: [ObjectWork::TYPE_RECREATION => 5, ...])
     * @return ArrangementModelFacade
     * @throws \yii\db\Exception
     */
    public function generateTerritoryArrangement(string $generateType, array $votes = [])
    {
        $this->manager->territory = TerritoryConcept::make(150, 150, TerritoryConcept::STEP);
        $cellsCount = $this->manager->territory->widthCellCount * $this->manager->territory->lengthCellCount;

        $this->manager->setTerritoryState(1, $generateType, $cellsCount, $votes);
        $this->manager->territory->setFullnessIntervals(
            [
                'sport' => [0, $this->manager->territory->state->sportPart / 2, $this->manager->territory->state->sportPart / 1.5, $this->manager->territory->state->sportPart / 1.2, $this->manager->territory->state->sportPart],
                'game' => [0, $this->manager->territory->state->gamePart / 2, $this->manager->territory->state->gamePart / 1.5, $this->manager->territory->state->gamePart / 1.2, $this->manager->territory->state->gamePart],
                'recreation' => [0, $this->manager->territory->state->recreationPart / 2, $this->manager->territory->state->recreationPart / 1.5, $this->manager->territory->state->recreationPart / 1.2, $this->manager->territory->state->recreationPart],
                'education' => [0, $this->manager->territory->state->educationPart / 2, $this->manager->territory->state->educationPart / 1.5, $this->manager->territory->state->educationPart / 1.2, $this->manager->territory->state->educationPart],
            ]
        );

        $endFlag = true;
        while (!$this->manager->isFilled() && $endFlag) {
            $endFlag = $this->manager->setSuitableObject();
        }

        $this->model = new ArrangementModelFacade($this->manager->territory->matrix, $this->manager->territory->state->objectIds, $this->manager->territory->state->objectsList);

        return $this->model;
    }

    /**
     * Установка объекта в заданную позицию
     * @param $object @see ObjectWork
     * @param int $left отступ от левого края
     * @param int $top отступ от верхнего края
     * @param int $position позиционирование объекта: TerritoryConcept::HORIZONTAL_POSITION / TerritoryConcept::VERTICAL_POSITION
     */
    public function installObject($object, $left, $top, $position)
    {
        $this->manager->installObject($object, $left, $top, $position);
        $this->model->setMatrix($this->manager->territory->matrix);
        $this->model->setObjectsCount($this->manager->territory->state->objectIds);
        $this->model->setObjectsList($this->manager->territory->state->objectsList);
    }

    /**
     * Удаление объекта из заданной позиции
     * @param $object @see ObjectWork
     * @param int $left отступ от левого края
     * @param int $top отступ от верхнего края
     * @param int $position позиционирование объекта: TerritoryConcept::HORIZONTAL_POSITION / TerritoryConcept::VERTICAL_POSITION
     */
    public function removeObject($object, $left, $top, $position)
    {
        $this->manager->removeObject($object, $left, $top, $position);
        $this->model->setMatrix($this->manager->territory->matrix);
        $this->model->setObjectsCount($this->manager->territory->state->objectIds);
        $this->model->setObjectsList($this->manager->territory->state->objectsList);
    }

    /**
     * Возвращает матрицу в сыром виде
     * @return array
     */
    public function getRawMatrix()
    {
        return $this->model->getRawMatrix();
    }

    /**
     * Возвращает список объектов с позиционированием
     * @return array
     */
    public function getObjectsPosition()
    {
        return $this->model->getObjectsList();
    }

    /**
     * Возвращает список объектов с количеством
     * @return array
     */
    public function getObjectsCount()
    {
        return $this->model->getObjectsCount();
    }
}