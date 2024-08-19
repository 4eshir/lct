<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class MapController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->session->set('header-active', 'map');

        return $this->render('index');
    }
}