<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\work\ObjectTypeWork;
use app\models\work\ObjectWork;
use yii\base\Security;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class FillController extends Controller
{
    const OBJECTS_COUNT = 10;

    const MIN_SIZE = 10;
    const MAX_SIZE = 6000;

    const MIN_COST = 1000;
    const MAX_COST = 100000;

    const MIN_CTIME = 10;
    const MAX_CTIME = 60;

    const MIN_ITIME = 1;
    const MAX_ITIME = 10;

    const MIN_WORKERS = 1;
    const MAX_WORKERS = 10;

    const CREATORS = [
        'Acme Corporation',
        'XYZ Industries',
        'Sunset Enterprises',
        'Pinnacle Group',
        'Nova Innovations',
        'Horizon Ventures',
        'Apex Labs',
        'Summit Solutions',
        'Eclipse Technologies',
        'Moonlight Co.'
    ];

    public function actionCreateObjects()
    {
        for ($i = 0; $i < self::OBJECTS_COUNT; $i++) {
            $object = new ObjectWork();
            $object->name = (new Security())->generateRandomString(20);
            $object->length = array_rand(range(self::MIN_SIZE, self::MAX_SIZE, 10));
            $object->width = array_rand(range(self::MIN_SIZE, self::MAX_SIZE, 10));
            $object->height = array_rand(range(self::MIN_SIZE, self::MAX_SIZE, 10));
            $object->cost = array_rand(range(self::MIN_COST, self::MAX_COST, 500));
            $object->created_time = array_rand(range(self::MIN_CTIME, self::MAX_CTIME));
            $object->install_time = array_rand(range(self::MIN_ITIME, self::MAX_ITIME));
            $object->worker_count = array_rand(range(self::MIN_WORKERS, self::MAX_WORKERS));

            $typeIds = ArrayHelper::getColumn(ObjectTypeWork::find()->all(), 'id');
            $object->object_type_id = array_rand($typeIds);
            $object->creator = self::CREATORS[array_rand(self::CREATORS)];
            $object->save();
        }
    }
}
