<?php


use app\components\arrangement\TerritoryConcept;
use app\models\ObjectExtended;
use app\models\work\ObjectWork;
use app\models\work\TerritoryWork;

class LocalCoordinatesManager
{
    public static function calculateLocalCoordinates($territory, $objectExt, $step = TerritoryConcept::STEP)
    {

        /** @var ObjectExtended $objectExt */
        /** @var TerritoryWork $territory */

        $centerObject = [
            'x' =>
                $objectExt->positionType == TerritoryConcept::HORIZONTAL_POSITION ?
                    ObjectWork::convertDistanceToCells($objectExt->left + $objectExt->object->length / 2, TerritoryConcept::STEP) :
                    ObjectWork::convertDistanceToCells($objectExt->top + $objectExt->object->width / 2, TerritoryConcept::STEP),
            'y' =>
                $objectExt->positionType == TerritoryConcept::HORIZONTAL_POSITION ?
                    ObjectWork::convertDistanceToCells($objectExt->top + $objectExt->object->width / 2, TerritoryConcept::STEP) :
                    ObjectWork::convertDistanceToCells($objectExt->left + $objectExt->object->length / 2, TerritoryConcept::STEP),
        ];

        $centerTerritory = [
            'x' => ObjectWork::convertDistanceToCells($territory->length, TerritoryConcept::STEP) / 2,
            'y' => ObjectWork::convertDistanceToCells($territory->width, TerritoryConcept::STEP) / 2,
        ];

        return [
            'x' => $centerObject['x'] - $centerTerritory['x'],
            'y' => $centerObject['y'] - $centerTerritory['y'],
        ];
    }
}