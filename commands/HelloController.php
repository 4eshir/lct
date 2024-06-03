<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\arrangement\TerritoryArrangementManager;
use app\components\arrangement\TerritoryConcept;
use app\facades\TerritoryFacade;
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
        $facade = Yii::createObject(TerritoryFacade::class);
        $matrixModel = $facade->generateTerritoryArrangement(TerritoryConcept::TYPE_BASE_WEIGHTS, 1, TerritoryFacade::OPTIONS_BUDGET_ECONOMY);
        $matrixModel->showMatrix(fopen('php://stdout', 'w'));

        $arModel = $facade->model;

        $facade1 = Yii::createObject(TerritoryFacade::class);
        $matrixModel1 = $facade1->generateTerritoryArrangement(TerritoryConcept::TYPE_BASE_WEIGHTS, 1, TerritoryFacade::OPTIONS_SIMILAR, ['arrangement' => $arModel->objectsPosition]);
        $matrixModel1->showMatrix(fopen('php://stdout', 'w'));
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
