<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\arrangement\TerritoryArrangementManager;
use app\components\arrangement\TerritoryConcept;
use app\helpers\MathHelper;
use app\models\work\AgesWeightWork;
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

        $weights = AgesWeightWork::find()->orderBy(['ages_interval_id' => SORT_ASC])->all();
        $recreation = 0;
        $sport = 0;
        $educational = 0;
        $game = 0;
        foreach ($weights as $weight) {

        }

        $manager->setTerritoryState();
        $manager->territory = TerritoryConcept::make(150, 350, TerritoryConcept::STEP);

        $object1 = ObjectWork::find()->where(['id' => 1])->one();

        $manager->installObject($object1, 0, 0, TerritoryConcept::HORIZONTAL_POSITION);
        $manager->installObject($object1, 0, 10, TerritoryConcept::HORIZONTAL_POSITION);
        $manager->installObject($object1, 0, 20, TerritoryConcept::HORIZONTAL_POSITION);
        //$manager->installObject($object1, 30, 0, TerritoryConcept::HORIZONTAL_POSITION);

        //var_dump($manager->allowedInstall($object1, 30, 0, TerritoryConcept::HORIZONTAL_POSITION));
        var_dump($manager->territory->state->sportPart);

        $manager->territory->showDebugMatrix(fopen('php://stdout', 'w'));
    }
}
