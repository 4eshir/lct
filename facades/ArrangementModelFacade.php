<?php


namespace app\facades;


use app\models\ObjectExtended;

class ArrangementModelFacade
{
    private $matrix; // матрица расстановки объектов
    private $objects; // список объектов формата [id => кол-во, ...]
    private $objectsPosition; /** список объектов @see ObjectExtended */

    public function __construct(array $matrix, array $objects, array $objectsList)
    {
        $this->matrix = $matrix;
        $this->objects = $objects;
        $this->objectsPosition = $objectsList;
    }

    public function getRawMatrix()
    {
        return $this->matrix;
    }

    public function getObjectsCount()
    {
        return $this->objects;
    }

    public function getObjectsList()
    {
        return $this->objectsPosition;
    }

    public function showMatrix($stream)
    {
        for ($i = 0; $i < count($this->matrix); $i++) {
            for ($j = 0; $j < count($this->matrix[$i]); $j++) {
                fwrite($stream, $this->matrix[$i][$j].' ');
            }
            fwrite($stream, "\n");
        }

        fwrite($stream, "\n");
    }

    public function setMatrix(array $matrix)
    {
        $this->matrix = $matrix;
    }

    public function setObjectsCount(array $objects)
    {
        $this->objects = $objects;
    }

    public function setObjectsList(array $objectsList)
    {
        $this->objectsPosition = $objectsList;
    }

    public function calculateBudget()
    {
        $sum = 0;
        foreach ($this->objectsPosition as $object) {
            /** @var ObjectExtended $object */
            $sum += $object->object->cost;
        }

        return $sum;
    }

    public function calculateCreatedTime()
    {
        $sum = 0;
        foreach ($this->objectsPosition as $object) {
            /** @var ObjectExtended $object */
            $sum += $object->object->created_time;
        }

        return $sum;
    }

    public function calculateInstallationTime()
    {
        $sum = 0;
        foreach ($this->objectsPosition as $object) {
            /** @var ObjectExtended $object */
            $sum += $object->object->install_time;
        }

        return $sum;
    }
}