<?php

namespace app\controllers;

use app\services\frontend\ResidentsService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class ResidentsController extends Controller
{
    // здесь выводим опросник и итоговое голосование

    private ResidentsService $service;

    public function __construct($id, $module, ResidentsService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }
}
