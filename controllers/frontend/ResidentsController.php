<?php

namespace app\controllers\frontend;

use app\models\forms\QuestionForm;
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

    public function actionStartQuestionnaire()
    {
        $text = 'Добро пожаловать в голосование';

        if (Yii::$app->request->post()) {
            return $this->redirect(['quest']);
        }

        return $this->render('quest-info', [
            'text' => $text,
        ]);
    }

    public function actionQuest()
    {
        $model = new QuestionForm();

        return $this->render('question', [
            'model' => $model,
        ]);
    }

    public function actionEndQuestionnaire()
    {
        $text = 'Голосование успешно завершено';

        $this->service->endVote();

        return $this->render('quest-info', [
            'text' => $text,
        ]);
    }
}
