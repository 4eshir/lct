<?php


namespace app\components\arrangement;


class TerritoryState
{
    public $sportPart;
    public $gamePart;
    public $recreationPart;
    public $educationalPart;

    public function fill($recreationPart, $sportPart, $educationalPart, $gamePart)
    {
        $this->recreationPart = $recreationPart;
        $this->sportPart = $sportPart;
        $this->educationalPart = $educationalPart;
        $this->gamePart = $gamePart;
    }
}