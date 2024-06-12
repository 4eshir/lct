<?php

namespace app\models\work;

use app\models\common\NeighboringTerritory;

class NeighboringTerritoryWork extends NeighboringTerritory
{
    public function getTerritoryWork()
    {
        return $this->hasOne(TerritoryWork::class, ['id' => 'territory_id']);
    }

}