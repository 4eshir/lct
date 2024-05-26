<?php


namespace app\facades;


class ArrangementModelFacade
{
    private $matrix; // матрица расстановки объектов
    private $objects; // список объектов формата [id => кол-во, ...]

    public function __construct(array $matrix, array $objects)
    {
        $this->matrix = $matrix;
        $this->objects = $objects;
    }

    public function getRawMatrix()
    {
        return $this->matrix;
    }

    public function getObjectsList()
    {
        return $this->objects;
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
}