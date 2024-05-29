<?php

namespace app\models\work;

use app\models\common\Territory;
use yii\helpers\ArrayHelper;

class TerritoryWork extends Territory
{

    public static function GetAllTerritories()
    {
        $data = TerritoryWork::find()->all();

        $result = ArrayHelper::map($data, 'id', 'name');

        return $result;
    }
}