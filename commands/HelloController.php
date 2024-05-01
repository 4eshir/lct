<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\arrangement\TerritoryArrangementManager;
use app\components\arrangement\TerritoryConcept;
use app\models\work\ObjectWork;
use Yii;
use yii\base\BaseObject;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    public function actionIndex()
    {
        $manager = Yii::createObject(TerritoryArrangementManager::class);
        $manager->territory = TerritoryConcept::make(100, 80, 10);

        $object1 = new ObjectWork();
        $object1->id = 12;
        $object1->length = 15;
        $object1->width = 10;
        $object1->dead_zone_size = 5;

        $object2 = new ObjectWork();
        $object2->id = 25;
        $object2->length = 22;
        $object2->width = 32;
        $object2->dead_zone_size = 6;

        $manager->territory->showDebugMatrix(fopen('php://stdout', 'w'));

        $manager->installObject($object1, 0, 2, TerritoryConcept::HORIZONTAL_POSITION);

        $manager->territory->showDebugMatrix(fopen('php://stdout', 'w'));

        $manager->installObject($object2, 3, 4, TerritoryConcept::HORIZONTAL_POSITION);

        $manager->territory->showDebugMatrix(fopen('php://stdout', 'w'));
    }
}
