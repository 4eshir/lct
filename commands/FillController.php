<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\work\NeighboringTerritoryWork;
use app\models\work\ObjectTypeWork;
use app\models\work\ObjectWork;
use app\models\work\PeopleTerritoryWork;
use app\models\work\TerritoryWork;
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

    public function actionPeopleTerritory($territoryId)
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

    public function actionFixedTerritories()
    {
        // Две базовые площадки
        $territory = new TerritoryWork();
        $territory->name = 'Детская площадка на 3-м Волоколамском проезде 14';
        $territory->address = '3-й Волоколамский проезд, д. 14, к. 1';
        $territory->length = 1500;
        $territory->width = 1100;
        $territory->latitude = 37.488615;
        $territory->longitude = 55.802664;
        $territory->save();
        $parentId1 = $territory->id;

        $territory = new TerritoryWork();
        $territory->name = 'Детская площадка на 1-м Волоколамском проезде 15/16';
        $territory->address = '1-й Волоколамский проезд, д. 15/16';
        $territory->length = 1800;
        $territory->width = 1700;
        $territory->latitude = 37.489101;
        $territory->longitude = 55.802706;
        $territory->save();
        $parentId2 = $territory->id;
        // --------------------

        // Делаем соседей
        $territory = new TerritoryWork();
        $territory->name = 'Детская площадка на 1-м Волоколамском проезде 13';
        $territory->address = '1-й Волоколамский проезд, д. 13';
        $territory->length = 0;
        $territory->width = 0;
        $territory->latitude = 37.489873;
        $territory->longitude = 55.802312;
        $territory->priority_type = 4;
        $territory->priority_coef = 0.6;
        $territory->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId1;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId2;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();


        $territory = new TerritoryWork();
        $territory->name = 'Детская площадка на 1-м Волоколамском проезде 8 к. 1';
        $territory->address = '1-й Волоколамский пр-д, 8 корпус 1';
        $territory->length = 0;
        $territory->width = 0;
        $territory->latitude = 37.491407;
        $territory->longitude = 55.799957;
        $territory->priority_type = 3;
        $territory->priority_coef = 0.5;
        $territory->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId1;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId2;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();


        $territory = new TerritoryWork();
        $territory->name = 'Детская площадка на Маршала Конева 2';
        $territory->address = 'Конева Маршала ул. 2, 4 к.1';
        $territory->length = 0;
        $territory->width = 0;
        $territory->latitude = 37.4927168;
        $territory->longitude = 55.8018517;
        $territory->priority_type = 2;
        $territory->priority_coef = 0.45;
        $territory->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId1;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId2;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();


        $territory = new TerritoryWork();
        $territory->name = 'Детская площадка на Большом Волоколамском проезде 12';
        $territory->address = 'Большой Волоколамский пр-д, 12';
        $territory->length = 0;
        $territory->width = 0;
        $territory->latitude = 37.4939807;
        $territory->longitude = 55.8017893;
        $territory->priority_type = 4;
        $territory->priority_coef = 0.5;
        $territory->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId1;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();

        $neighbor = new NeighboringTerritoryWork();
        $neighbor->territory_id = $parentId2;
        $neighbor->neighboring_territory_id = $territory->id;
        $neighbor->save();
        // --------------

    }

    public function actionFixedObjects()
    {
        $object = new ObjectWork();
        $object->name = 'Турник F';
        $object->length = 200;
        $object->width = 200;
        $object->height = 220;
        $object->cost = 60000;
        $object->created_time = 10;
        $object->install_time = 1;
        $object->worker_count = 2;
        $object->object_type_id = 2;
        $object->creator = 'Кенгуру';
        $object->dead_zone_size = 50;
        $object->style = 'Экостиль';
        $object->article = '925087231';
        $object->left_age = 5;
        $object->right_age = 130;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Брусья F';
        $object->length = 150;
        $object->width = 70;
        $object->height = 100;
        $object->cost = 25000;
        $object->created_time = 8;
        $object->install_time = 1;
        $object->worker_count = 1;
        $object->object_type_id = 2;
        $object->creator = 'Sportmen';
        $object->dead_zone_size = 50;
        $object->style = 'Экостиль';
        $object->article = '925087233';
        $object->left_age = 5;
        $object->right_age = 130;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Комплекс детский F';
        $object->length = 400;
        $object->width = 500;
        $object->height = 220;
        $object->cost = 45000;
        $object->created_time = 14;
        $object->install_time = 2;
        $object->worker_count = 4;
        $object->object_type_id = 4;
        $object->creator = 'Лебер';
        $object->dead_zone_size = 100;
        $object->style = 'Маверикс';
        $object->article = '925087227';
        $object->left_age = 3;
        $object->right_age = 12;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Качели стационарные F';
        $object->length = 350;
        $object->width = 120;
        $object->height = 230;
        $object->cost = 20000;
        $object->created_time = 11;
        $object->install_time = 1;
        $object->worker_count = 1;
        $object->object_type_id = 4;
        $object->creator = 'Аданат';
        $object->dead_zone_size = 50;
        $object->style = 'Лемурия';
        $object->article = '925087197';
        $object->left_age = 2;
        $object->right_age = 18;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Качели балансир F';
        $object->length = 250;
        $object->width = 50;
        $object->height = 80;
        $object->cost = 18000;
        $object->created_time = 6;
        $object->install_time = 1;
        $object->worker_count = 1;
        $object->object_type_id = 4;
        $object->creator = 'Аданат';
        $object->dead_zone_size = 50;
        $object->style = 'Лемурия';
        $object->article = '925087205';
        $object->left_age = 4;
        $object->right_age = 16;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Песочница F';
        $object->length = 200;
        $object->width = 200;
        $object->height = 50;
        $object->cost = 34000;
        $object->created_time = 3;
        $object->install_time = 2;
        $object->worker_count = 2;
        $object->object_type_id = 3;
        $object->creator = 'ДиКом';
        $object->dead_zone_size = 50;
        $object->style = 'Тематика25';
        $object->article = '925087221';
        $object->left_age = 0;
        $object->right_age = 5;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Качалка F';
        $object->length = 80;
        $object->width = 60;
        $object->height = 80;
        $object->cost = 11000;
        $object->created_time = 2;
        $object->install_time = 1;
        $object->worker_count = 1;
        $object->object_type_id = 4;
        $object->creator = 'ДиКом';
        $object->dead_zone_size = 50;
        $object->style = 'Тематика25';
        $object->article = '925087199';
        $object->left_age = 2;
        $object->right_age = 10;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Карусель F';
        $object->length = 150;
        $object->width = 150;
        $object->height = 80;
        $object->cost = 42000;
        $object->created_time = 10;
        $object->install_time = 1;
        $object->worker_count = 2;
        $object->object_type_id = 4;
        $object->creator = 'ДиКом';
        $object->dead_zone_size = 50;
        $object->style = 'Тематика17';
        $object->article = '925087207';
        $object->left_age = 2;
        $object->right_age = 10;
        $object->save();

        $object = new ObjectWork();
        $object->name = 'Скамейка F';
        $object->length = 210;
        $object->width = 70;
        $object->height = 80;
        $object->cost = 25000;
        $object->created_time = 5;
        $object->install_time = 1;
        $object->worker_count = 1;
        $object->object_type_id = 1;
        $object->creator = 'Атрикс';
        $object->dead_zone_size = 50;
        $object->style = 'Экостиль';
        $object->article = '925087212';
        $object->left_age = 2;
        $object->right_age = 130;
        $object->save();

    }
}
