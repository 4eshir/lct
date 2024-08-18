<?php


namespace app\controllers;


use app\models\forms\QuestionForm;
use Yii;
use yii\web\Controller;

class RevisorController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSurvey()
    {
        $model = new QuestionForm();

        if (Yii::$app->request->post()) {
            return $this->render('index');
        }

        return $this->render('survey', [
            'model' => $model
        ]);
    }
}