<?php

namespace app\controllers\frontend;

use app\models\forms\ChooseTerritoryForm;
use app\services\frontend\AdministrationService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class AdministrationController extends Controller
{
    // здесь выводим только конструктор расстановки, остальное - в backend

    private AdministrationService $service;

    public function __construct($id, $module, AdministrationService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionIndex()
    {
        $model = new ChooseTerritoryForm();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionContructor()
    {

    }
}
