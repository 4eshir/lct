<?php

namespace app\controllers\backend;

use app\models\forms\ChooseTerritoryForm;
use app\models\forms\ConstructorTerritoryForm;
use app\services\backend\AdminService;
use app\services\frontend\AdministrationService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class AdminController extends Controller
{
    private AdminService $service;

    public function __construct($id, $module, AdminService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionIndex()
    {
        Yii::$app->session->set('header-active', 'admin-panel');

        $model = new ChooseTerritoryForm();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            return $this->redirect(['constructor', 'tId' => $model->territoryId]);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionConstructor($tId)
    {
        $model = new ConstructorTerritoryForm($tId);

        return $this->render('constructor', [
            'model' => $model
        ]);
    }
}
