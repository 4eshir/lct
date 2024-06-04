<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\work\TerritoryWork $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => 'Админ-панель',
    'url' => ['/backend/admin/index'],
];
$this->params['breadcrumbs'][] = ['label' => 'Список территорий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="territory-work-view main-block-admin">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'length',
            'width',
            'name',
        ],
    ]) ?>

</div>
