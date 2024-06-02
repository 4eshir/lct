<?php

namespace app\facades;

use app\components\arrangement\TerritoryArrangementManager;
use app\components\arrangement\TerritoryConcept;
use app\models\work\ObjectWork;
use app\models\work\TerritoryWork;

class TerritoryFacade
{
    const BUDGET_DEFAULT = 0;
    const BUDGET_ECONOMY = 1;

    public ArrangementModelFacade $model;
    public TerritoryArrangementManager $manager;

    public function __construct(TerritoryArrangementManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Генерация расстановки объектов на территории
     * @param string $generateType тип генерации из списка констант @see TerritoryConcept
     * @param int $territoryId id территории @see TerritoryWork
     * @param int $budgetType тип генерации расстановки по бюджету (self::BUDGET_DEFAULT - без учета бюджета, self::BUDGET_ECONOMY - максимально экономный вариант)
     * @param array $votes необязательный параметр, массив голосов пользователя (пример: [ObjectWork::TYPE_RECREATION => 5, ...])
     * @return ArrangementModelFacade
     * @throws \yii\db\Exception
     */
    public function generateTerritoryArrangement(string $generateType, int $territoryId, int $budgetType = self::BUDGET_DEFAULT, array $votes = [])
    {
        $this->manager->setTerritory($generateType, $territoryId, $votes);

        $referenceUnitCost = null;
        $referenceEmptyCells = null;
        if ($budgetType == 1) {
            $endFlag = true;
            while (!$this->manager->isFilled() && $endFlag) {
                $endFlag = $this->manager->setSuitableObject();
            }

            $referenceUnitCost = $this->manager->territory->calculateUnitCost();
            $referenceEmptyCells = $this->manager->territory->calculateEmptyCells();

            $this->manager->setTerritory($generateType, $territoryId, $votes);
        }

        $endFlag = true;

        while (!$this->manager->isFilled() && $endFlag) {
            $endFlag = $this->manager->setSuitableObject($budgetType, $referenceUnitCost, $referenceEmptyCells);
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