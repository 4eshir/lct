<?php

namespace app\controllers;

use app\helpers\ApiHelper;
use app\models\common\ObjectT;
use app\models\common\User;
use app\models\work\AgesWeightChangeableWork;
use app\models\work\ObjectWork;
use app\models\work\TerritoryWork;
use app\services\ApiService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class ApiController extends Controller
{
    private ApiService $service;

    public function __construct($id, $module, ApiService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionDoc()
    {
        return $this->render('doc');
    }

    /**
     * Возвращает список всех МАФ, удовлетворяющих условиям в формате JSON
     * @param int $type ограничение по типу объектов
     * @param int $minCost ограничение по минимальной стоимости МАФ
     * @param int $maxCost ограничения по максимальной стоимости МАФ
     * @param string $style ограничения по стилю МАФ
     * @return false|string
     */
    public function actionGetObjects($type = null, $minCost = null, $maxCost = null, $style = null)
    {
        if (!$this->service->checkKey(ApiHelper::TEST_API_KEY)) {
            return json_encode(['result' => ApiHelper::STATUS_ERROR, 'error_message' => 'Incorrect API key']);
        }

        $query = ObjectWork::find();

        if ($type !== null) {
            $query->andWhere(['object_type_id' => $type]);
        }

        if ($minCost !== null) {
            $query->andWhere(['>=', 'cost', $minCost]);
        }

        if ($maxCost !== null) {
            $query->andWhere(['<=', 'cost', $maxCost]);
        }

        if ($style !== null) {
            $query->andWhere(['style' => $style]);
        }

        $data['result'] = ApiHelper::STATUS_SUCCESS;
        foreach ($query->all() as $item) {
            $data['data'][] = $item->attributes;
        }

        return json_encode($data);
    }

    /**
     * Возвращает список дворовых территорий
     * @return array|false|string
     */
    public function actionGetTerritories()
    {
        if (!$this->service->checkKey(ApiHelper::TEST_API_KEY)) {
            return json_encode(['result' => ApiHelper::STATUS_ERROR, 'error_message' => 'Incorrect API key']);
        }

        $query = TerritoryWork::find();

        $data['result'] = ApiHelper::STATUS_SUCCESS;
        foreach ($query->all() as $item) {
            $data['data'][] = $item->attributes;
        }

        return $data;
    }

    /**
     * Возвращает список измененных весов по возрастам, с учетом заданных параметров
     * @param $tId
     * @param $weightType
     * @param $agesInterval
     * @return array
     */
    public function actionGetRealWeights($tId, $weightType = null, $agesInterval = null)
    {
        $query = AgesWeightChangeableWork::find()->where(['territory_id' => $tId]);

        if ($agesInterval !== null) {
            $query = $query->andWhere(['ages_interval_id' => $agesInterval]);
        }

        $data['result'] = ApiHelper::STATUS_SUCCESS;
        foreach ($query->all() as $item) {
            $data['data'][] = $item->attributes;
        }

        return $data;
    }

}