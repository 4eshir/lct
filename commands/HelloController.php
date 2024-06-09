<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\analytic\ObjectAnalytic;
use app\components\arrangement\TemplatesManager;
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
use yii\helpers\ArrayHelper;

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
        $matrixModel = $facade->generateTerritoryArrangement(TerritoryConcept::TYPE_BASE_WEIGHTS, 1, TerritoryFacade::OPTIONS_DEFAULT);
        $matrixModel->showMatrix(fopen('php://stdout', 'w'));

        $arModel = $facade->model;

        $facade1 = Yii::createObject(TerritoryFacade::class);
        $matrixModel1 = $facade1->generateTerritoryArrangement(TerritoryConcept::TYPE_BASE_WEIGHTS, 1, TerritoryFacade::OPTIONS_SIMILAR, ['arrangement' => $arModel->objectsPosition]);
        $matrixModel1->showMatrix(fopen('php://stdout', 'w'));
    }

    public function actionTest()
    {
        var_dump('lct2024task3');
        var_dump(md5('lct2024task3'));
        var_dump(md5('lct2024task3'));
    }

    public function actionAnalytic()
    {
        $targetObject = ObjectWork::find()->where(['id' => 15])->one();

        $analModel = new ObjectAnalytic();
        var_dump(ArrayHelper::getColumn($analModel->findSimilarObjects($targetObject, ObjectAnalytic::SIMILAR_TYPE_SLIGHTLY), 'id'));
    }

    public function actionTemplate()
    {
        $template = new TemplatesManager();
        $template->generateTemplateMatrix(1, 20, 20);
        $template->showDebugTemplateMatrix(fopen('php://stdout', 'w'));
    }
}
