<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\work\QuestionnaireWork $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = [
    'label' => 'Админ-панель',
    'url' => ['/backend/admin/index'],
];
$this->params['breadcrumbs'][] = ['label' => 'Результаты опросов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="questionnaire-work-view main-block-admin">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'ages_interval_id',
            'sport_tendency',
            'recreation_tendency',
            'game_tendency',
            'education_tendency',
            'arrangement_matrix:ntext',
            'territory_id',
        ],
    ]) ?>

</div>
