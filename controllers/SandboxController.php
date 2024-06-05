<?php

namespace app\controllers;

use app\facades\TerritoryFacade;
use app\helpers\ApiHelper;
use app\models\common\ObjectT;
use app\models\common\User;
use app\models\forms\sandbox\GetObjectsForm;
use app\models\work\AgesWeightChangeableWork;
use app\models\work\ObjectWork;
use app\models\work\TerritoryWork;
use app\services\ApiService;
use Yii;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SandboxController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionGetObjects()
    {
        $model = new GetObjectsForm();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            $query = ObjectWork::find();

            if ($model->type !== "") {
                $query->andWhere(['object_type_id' => $model->type]);
            }

            if ($model->minCost !== "") {
                $query->andWhere(['>=', 'cost', $model->minCost]);
            }

            if ($model->maxCost !== "") {
                $query->andWhere(['<=', 'cost', $model->maxCost]);
            }

            if ($model->style !== "") {
                $query->andWhere(['style' => $model->style]);
            }

            $data['result'] = ApiHelper::STATUS_SUCCESS;
            foreach ($query->all() as $item) {
                $data['data'][] = $item->attributes;
            }

            return json_encode($data['data']);
        }

        return $this->render('get-objects', [
            'model' => $model,
        ]);
    }

    /**
     * Возвращает список дворовых территорий
     * @return array|false|string
     */
    public function actionGetTerritories()
    {
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

    public function actionGenerateArrangement($tId, $genType, $addGenType = TerritoryFacade::OPTIONS_DEFAULT, $params = [])
    {
        $facade = Yii::createObject(TerritoryFacade::class);
        $facade->generateTerritoryArrangement($genType, $tId, $addGenType, $params);

        return json_encode($facade->getRawMatrix());
    }

    public function actionLoadTerritory()
    {
        if ($request = Yii::$app->request->post()) {
            $territory = new TerritoryWork();
            $territory->width = $request['width'];
            $territory->length = $request['length'];
            $territory->name = $request['name'];
            $territory->address = $request['address'];
            $territory->latitude = $request['latitude'];
            $territory->longitude = $request['longitude'];
            if ($territory->save()) {
                return json_encode(['result' => ApiHelper::STATUS_SUCCESS]);
            }
            else {
                return json_encode(['result' => ApiHelper::STATUS_ERROR, 'error_message' => 'Saving error, try again']);
            }
        }
        else {
            return json_encode(['result' => ApiHelper::STATUS_ERROR, 'error_message' => 'Incorrect query type. Use POST type']);
        }
    }

    public function actionLoadObject()
    {
        if ($request = Yii::$app->request->post()) {
            $object = new ObjectWork();
            $object->name = $request['name'];
            $object->length = $request['length'];
            $object->width = $request['width'];
            $object->height = $request['height'];
            $object->cost = $request['cost'];
            $object->created_time = array_key_exists('created_time', $request) ? $request['created_time'] : 0;
            $object->install_time = array_key_exists('install_time', $request) ? $request['install_time'] : 0;
            $object->worker_count = array_key_exists('worker_count', $request) ? $request['worker_count'] : 0;
            $object->object_type_id = $request['object_type_id'];
            $object->creator = $request['creator'];
            $object->dead_zone_size = array_key_exists('dead_zone_size', $request) ? $request['dead_zone_size'] : 0;
            $object->style = array_key_exists('style', $request) ? $request['style'] : 'default';
            $object->article = $request['article'];
            $object->left_age = array_key_exists('left_age', $request) ? $request['left_age'] : null;
            $object->right_age = array_key_exists('right_age', $request) ? $request['right_age'] : null;

            if ($object->save()) {
                return json_encode(['result' => ApiHelper::STATUS_SUCCESS]);
            }
            else {
                return json_encode(['result' => ApiHelper::STATUS_ERROR, 'error_message' => 'Saving error, try again']);
            }
        }
        else {
            return json_encode(['result' => ApiHelper::STATUS_ERROR, 'error_message' => 'Incorrect query type. Use POST type']);
        }
    }
}