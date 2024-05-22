<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\arrangement\TerritoryArrangementManager;
use app\components\arrangement\TerritoryConcept;
use app\helpers\FuzzyLogicHelper;
use app\helpers\MathHelper;
use app\models\FuzzyIntervals;
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
        $manager->territory = TerritoryConcept::make(150, 150, TerritoryConcept::STEP);
        $cellsCount = $manager->territory->widthCellCount * $manager->territory->lengthCellCount;

        $manager->setTerritoryState(1, TerritoryConcept::TYPE_BASE_WEIGHTS, $cellsCount);
        $manager->territory->setFullnessIntervals(
            [
                'sport' => [0, $manager->territory->state->sportPart / 2, $manager->territory->state->sportPart / 1.5, $manager->territory->state->sportPart / 1.2, $manager->territory->state->sportPart],
                'game' => [0, $manager->territory->state->gamePart / 2, $manager->territory->state->gamePart / 1.5, $manager->territory->state->gamePart / 1.2, $manager->territory->state->gamePart],
                'recreation' => [0, $manager->territory->state->recreationPart / 2, $manager->territory->state->recreationPart / 1.5, $manager->territory->state->recreationPart / 1.2, $manager->territory->state->recreationPart],
                'education' => [0, $manager->territory->state->educationPart / 2, $manager->territory->state->educationPart / 1.5, $manager->territory->state->educationPart / 1.2, $manager->territory->state->educationPart],
            ]
        );

        $object1 = ObjectWork::find()->where(['id' => 1])->one();

        $fills = [
            ObjectWork::TYPE_RECREATION => $manager->territory->state->recreationPart,
            ObjectWork::TYPE_SPORT => $manager->territory->state->sportPart,
            ObjectWork::TYPE_EDUCATION => $manager->territory->state->educationPart,
            ObjectWork::TYPE_GAME => $manager->territory->state->gamePart,
        ];

        var_dump($fills);

        $endFlag = true;
        while (!$manager->isFilled() && $endFlag) {
            var_dump('boobs');
            $endFlag = $manager->getSuitableObject();
        }
        //$manager->installObject($object1, 0, 0, TerritoryConcept::HORIZONTAL_POSITION);
        //$manager->installObject($object1, 0, 10, TerritoryConcept::HORIZONTAL_POSITION);
        //$manager->installObject($object1, 0, 20, TerritoryConcept::HORIZONTAL_POSITION);
        //$manager->installObject($object1, 30, 0, TerritoryConcept::HORIZONTAL_POSITION);


        $manager->territory->showDebugMatrix(fopen('php://stdout', 'w'));
    }

    public function actionTest()
    {
        $fuzzy = new FuzzyIntervals();
        $fuzzy->createIntervals([0, 2, 4, 6]);

        for ($i = 0; $i < count($fuzzy->intervals); $i++) {
            if ($i == 0) {
                $intervalType = FuzzyIntervals::INTERVAL_TYPE_START;
            } else if ($i == count($fuzzy->intervals) - 1) {
                $intervalType = FuzzyIntervals::INTERVAL_TYPE_END;
            } else {
                $intervalType = FuzzyIntervals::INTERVAL_TYPE_MIDDLE;
            }

            var_dump(FuzzyLogicHelper::calculateSafeInterval($fuzzy->intervals[$i], $intervalType));
        }

        var_dump($fuzzy->belongToInterval(4.6));
    }
}
