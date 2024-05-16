<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\work\ObjectTypeWork;
use app\models\work\ObjectWork;
use app\models\work\PeopleTerritoryWork;
use yii\base\BaseObject;
use yii\base\Security;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class FillController extends Controller
{
    const OBJECTS_COUNT = 50;

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
            $object->length = range(self::MIN_SIZE, self::MAX_SIZE, 10)[array_rand(range(self::MIN_SIZE, self::MAX_SIZE, 10))];
            $object->width = range(self::MIN_SIZE, self::MAX_SIZE, 10)[array_rand(range(self::MIN_SIZE, self::MAX_SIZE, 10))];
            $object->height = range(self::MIN_SIZE, self::MAX_SIZE, 10)[array_rand(range(self::MIN_SIZE, self::MAX_SIZE, 10))];
            $object->cost = range(self::MIN_COST, self::MAX_COST)[array_rand(range(self::MIN_COST, self::MAX_COST, 500))];
            $object->created_time = range(self::MIN_CTIME, self::MAX_CTIME)[array_rand(range(self::MIN_CTIME, self::MAX_CTIME))];
            $object->install_time = range(self::MIN_ITIME, self::MAX_ITIME)[array_rand(range(self::MIN_ITIME, self::MAX_ITIME))];
            $object->worker_count = range(self::MIN_WORKERS, self::MAX_WORKERS)[array_rand(range(self::MIN_WORKERS, self::MAX_WORKERS))];

            $typeIds = ArrayHelper::getColumn(ObjectTypeWork::find()->all(), 'id');
            $object->object_type_id = $typeIds[array_rand($typeIds)];
            $object->creator = self::CREATORS[array_rand(self::CREATORS)];
            $object->save();
        }
    }

    public function actionFillPeople($territoryId)
    {
        $entity = new PeopleTerritoryWork();
        $entity->territory_id = $territoryId;
        $entity->ages_interval_id = 1;
        $entity->count = mt_rand(0, 100);
        $entity->save();

        $entity = new PeopleTerritoryWork();
        $entity->territory_id = $territoryId;
        $entity->ages_interval_id = 2;
        $entity->count = mt_rand(0, 100);
        $entity->save();

        $entity = new PeopleTerritoryWork();
        $entity->territory_id = $territoryId;
        $entity->ages_interval_id = 3;
        $entity->count = mt_rand(0, 100);
        $entity->save();

        $entity = new PeopleTerritoryWork();
        $entity->territory_id = $territoryId;
        $entity->ages_interval_id = 4;
        $entity->count = mt_rand(0, 100);
        $entity->save();

        $entity = new PeopleTerritoryWork();
        $entity->territory_id = $territoryId;
        $entity->ages_interval_id = 5;
        $entity->count = mt_rand(0, 100);
        $entity->save();

        $entity = new PeopleTerritoryWork();
        $entity->territory_id = $territoryId;
        $entity->ages_interval_id = 6;
        $entity->count = mt_rand(0, 100);
        $entity->save();

        $entity = new PeopleTerritoryWork();
        $entity->territory_id = $territoryId;
        $entity->ages_interval_id = 7;
        $entity->count = mt_rand(0, 100);
        $entity->save();
    }
}
