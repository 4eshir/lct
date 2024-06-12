<?php

namespace app\controllers\backend;

use app\components\arrangement\TemplatesManager;
use app\components\arrangement\TerritoryConcept;
use app\facades\TerritoryFacade;
use app\models\ObjectExtended;
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
        $matrix = $this->facade->getRawMatrix();
        $objectsList = $this->facade->model->objectsPosition;
        $resultObjList = [];
        $maxHeight = 0;

        foreach ($objectsList as $objectExt) {
            /** @var ObjectExtended $objectExt */
            if ($maxHeight < $objectExt->object->height) {
                $maxHeight = $objectExt->object->height;
            }

            $resultObjList[] = [
                'id' => $objectExt->object->id,
                'height' => $objectExt->object->height,
                'width' => $objectExt->object->width,
                'length' => $objectExt->object->length,
                'rotate' => $objectExt->positionType == TerritoryConcept::HORIZONTAL_POSITION ? TerritoryConcept::HORIZONTAL_POSITION : 90,
                'link' => $objectExt->object->model_path,
                'dotCenter' => [
                    'x' =>
                        $objectExt->positionType == TerritoryConcept::HORIZONTAL_POSITION ?
                            $objectExt->left + $objectExt->object->length / 2 :
                            $objectExt->top + $objectExt->object->width / 2,
                    'y' =>
                        $objectExt->positionType == TerritoryConcept::HORIZONTAL_POSITION ?
                            $objectExt->top + $objectExt->object->width / 2 :
                            $objectExt->left + $objectExt->object->length / 2,
                ],
            ];
        }

        return json_encode(
            [
                'result' => [
                    'matrixCount' => [
                        'width' => count($matrix[0]),
                        'height' => count($matrix),
                        'maxHeight' => $maxHeight,
                    ],
                    'matrix' => $matrix,
                    'objects' => $resultObjList,
                ],
            ],
        );
    }
}
