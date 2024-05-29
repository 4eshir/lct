<?php

namespace app\controllers\frontend;

use app\models\common\Questionnaire;
use app\models\forms\QuestionDecisionForm;
use app\models\forms\QuestionForm;
use app\models\work\QuestionnaireWork;
use app\models\work\UserWork;
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
            Yii::$app->cache->set('questionnaire', serialize($model));
            return $this->redirect(['final-decision']);
        }

        return $this->render('questionnaire', [
            'model' => $model,
        ]);
    }

    public function actionFinalDecision()
    {
        /** @var QuestionForm $questionForm */
        $questionForm = unserialize(Yii::$app->cache->get('questionnaire'));
        $model = new QuestionDecisionForm();
        $model->generateVariants(
            [
                $questionForm->answersRecreationCoef,
                $questionForm->answersSportCoef,
                $questionForm->answersEducationalCoef,
                $questionForm->answersGameCoef
            ],
            $questionForm->territory
        );

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            /** @var QuestionForm $oldAnswers */
            $oldAnswers = unserialize(Yii::$app->cache->get('questionnaire'));
            $questionnaire = new QuestionnaireWork(
                UserWork::getAuthUser()->id,
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

        return $this->render('quest-end', [
            'text' => $text,
        ]);
    }
}
