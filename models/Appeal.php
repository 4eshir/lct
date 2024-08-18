<?php


namespace app\models;


use yii\base\Model;

class Appeal extends Model
{
    const MTYPE_NORMAL = 'нормальный';
    const MTYPE_ELECTRIC = 'электричество';
    const MTYPE_SEWERAGE = 'канализация';
    const MTYPE_CONNECTION = 'связь';
    const MTYPE_WATER_SUPPLY = 'водоснабжение';
    const MTYPE_PERSONAL = 'личное';

    public $message;

    public function rules()
    {
        return [
            ['message', 'string']
        ];
    }
}