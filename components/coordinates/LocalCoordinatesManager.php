<?php


use app\components\arrangement\TerritoryConcept;
use app\models\ObjectExtended;
use app\models\work\TerritoryWork;

class LocalCoordinatesManager
{
    public static function calculateLocalCoordinates($territory, $objectExt, $step = TerritoryConcept::STEP)
    {

        /** @var ObjectExtended $objectExt */
        /** @var TerritoryWork $territory */

        $center = [
            'x' =>
                $objectExt->positionType == TerritoryConcept::HORIZONTAL_POSITION ?
                    $objectExt->left + $objectExt->object->length / 2 :
                    $objectExt->top + $objectExt->object->width / 2,
            'y' =>
                $objectExt->positionType == TerritoryConcept::HORIZONTAL_POSITION ?
                    $objectExt->top + $objectExt->object->width / 2 :
                    $objectExt->left + $objectExt->object->length / 2,
        ];


    }
}