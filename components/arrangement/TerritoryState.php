<?php


namespace app\components\arrangement;


use app\models\work\ObjectWork;
use yii\db\Exception;

class TerritoryState
{
    /**
     * Количество "ячеек" под каждый из типов объектов
     */
    public $sportPart;
    public $gamePart;
    public $recreationPart;
    public $educationPart;

    public $fillSport;
    public $fillGame;
    public $fillRecreation;
    public $fillEducation;

    public function fill($recreationPart, $sportPart, $educationalPart, $gamePart)
    {
        $this->recreationPart = $recreationPart;
        $this->sportPart = $sportPart;
        $this->educationPart = $educationalPart;
        $this->gamePart = $gamePart;

        $this->fillSport = 0;
        $this->fillGame = 0;
        $this->fillRecreation = 0;
        $this->fillEducation = 0;
    }

    public function addSport($cells)
    {
        if ($this->fillSport + $cells > $this->sportPart) {
            throw new Exception('Недостаточно места для размещения объекта спортивного типа');
        }

        $this->fillSport += $cells;
    }

    public function addGame($cells)
    {
        if ($this->fillGame + $cells > $this->gamePart) {
            throw new Exception('Недостаточно места для размещения объекта игрового типа');
        }

        $this->fillGame += $cells;
    }

    public function addRecreation($cells)
    {
        if ($this->fillRecreation + $cells > $this->recreationPart) {
            throw new Exception('Недостаточно места для размещения объекта рекреационного типа');
        }

        $this->fillRecreation += $cells;
    }

    public function addEducation($cells)
    {
        if ($this->fillEducation + $cells > $this->educationPart) {
            throw new Exception('Недостаточно места для размещения объекта развивающего типа');
        }

        $this->fillEducation += $cells;
    }

    public function getMinimumFilledType(array $fills)
    {
        $minimum = min($fills);
        switch ($minimum) {
            case $this->fillRecreation:
                return ObjectWork::TYPE_RECREATION;
            case $this->fillSport:
                return ObjectWork::TYPE_SPORT;
            case $this->fillGame:
                return ObjectWork::TYPE_GAME;
            case $this->fillEducation:
                return ObjectWork::TYPE_EDUCATION;
            default:
                throw new Exception('Неизвестный тип объекта');
        }
    }
}