<?php

namespace app\controllers\backend;

use app\components\arrangement\TemplatesManager;
use app\components\arrangement\TerritoryConcept;
use app\facades\TerritoryFacade;
use app\models\work\TerritoryWork;
use app\models\search\SearchTerritoryWork;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TerritoryController implements the CRUD actions for TerritoryWork model.
 */
class DemoController extends Controller
{
    private TemplatesManager $template;
    private TerritoryFacade $facade;

    public function __construct($id, $module, TemplatesManager $template, TerritoryFacade $facade, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->template = $template;
        $this->facade = $facade;
    }

    public function actionGenerateTemplateAjax($tId)
    {
        $this->facade->generateTerritoryArrangement(TerritoryConcept::TYPE_BASE_WEIGHTS, 1, TerritoryFacade::OPTIONS_DEFAULT, $tId);
        return json_encode(['result' => $this->facade->getRawMatrix()]);
    }
}
