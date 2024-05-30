<?php

namespace app\services\frontend;

use app\components\arrangement\TerritoryArrangementManager;
use app\facades\ArrangementModelFacade;

class ResidentsService
{

    public function endVote(ArrangementModelFacade $model, $generateType)
    {
        TerritoryArrangementManager::saveArrangement($model, $generateType);
    }
}