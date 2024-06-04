<?php

use app\models\work\AgesWeightWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\SearchAgesWeightWork $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Приоритетные веса по возрастам';
$this->params['breadcrumbs'][] = [
    'label' => 'Админ-панель',
    'url' => ['/backend/admin/index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ages-weight-work-index main-block-admin">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'self_weight',
            'sport_weight',
            'game_weight',
            'education_weight',
            //'recreation_weight',
            //'ages_interval_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, AgesWeightWork $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
