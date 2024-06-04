<?php

namespace app\models\work;

use app\models\common\Territory;
use yii\helpers\ArrayHelper;

class TerritoryWork extends Territory
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'length' => 'Длина (в см)',
            'width' => 'Ширина (в см)',
            'name' => 'Название',
        ];
    }

    public static function GetAllTerritories()
    {
        $data = TerritoryWork::find()->all();

        $result = ArrayHelper::map($data, 'id', 'name');

        return $result;
    }
}