<?php

use app\models\work\QuestionnaireWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\SearchQuestionnaireWork $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Результаты опросов';
$this->params['breadcrumbs'][] = [
    'label' => 'Админ-панель',
    'url' => ['/backend/admin/index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="questionnaire-work-index main-block-admin">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'user_id',
            'ages_interval_id',
            'sport_tendency',
            'recreation_tendency',
            //'game_tendency',
            //'education_tendency',
            //'arrangement_matrix:ntext',
            //'territory_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuestionnaireWork $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
