<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\SearchObjectWork $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="object-work-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'flType')->dropDownList([
        1 => 'Найти максимально схожие МАФ',
        2 => 'Найти достаточно схожие МАФ',
        3 => 'Найти немного схожие МАФ',
    ])->label('Поиск аналогичных МАФ') ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сбросить фильтры', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
