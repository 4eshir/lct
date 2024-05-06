<?php

namespace app\controllers\frontend;

use app\models\common\Questionnaire;
use app\models\forms\QuestionDecisionForm;
use app\models\forms\QuestionForm;
use app\models\work\QuestionnaireWork;
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
        $model = new QuestionForm();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            Yii::$app->cache->set('questionnaire', $model);
            return $this->redirect(['final-decision']);
        }

        return $this->render('questionnaire', [
            'model' => $model,
        ]);
    }

    public function actionFinalDecision()
    {
        $model = new QuestionDecisionForm();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            /** @var QuestionForm $oldAnswers */
            $oldAnswers = Yii::$app->cache->get('questionnaire');
            $questionnaire = new QuestionnaireWork(
                null,
                $oldAnswers->answerAge,
                $oldAnswers->answersSportCoef,
                $oldAnswers->answersRecreationCoef,
                $oldAnswers->answersGameCoef,
                $oldAnswers->answersEducationalCoef,
                $model->decision
            );
            $questionnaire->save();
            return $this->redirect(['end-questionnaire']);
        }

        return $this->render('final-decision', [
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
