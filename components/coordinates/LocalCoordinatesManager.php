<?php


use app\models\ObjectExtended;

class LocalCoordinatesManager
{
    public static function calculateLocalCoordinates($objectExt)
    {
        /** @var ObjectExtended $objectExt */

        $center = [
            'x' => $objectExt->left + $objectExt->object->length / 2,
            'y' => $objectExt->top + $objectExt->object->width / 2,
        ];
    }
}