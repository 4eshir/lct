<?php

/** @var $model QuestionForm */

use app\models\forms\QuestionForm;
use app\models\work\AgesIntervalWork;
use yii\helpers\Html;
use yii\jui\SliderInput;
use yii\widgets\ActiveForm;
?>


<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'answerAge')->dropDownList(AgesIntervalWork::GetAllIntervals()) ?>

<?= $form->field($model, 'answersSportCoef')->widget(SliderInput::classname(), [
    'clientOptions' => [
        'min' => 1,
        'max' => 10,
    ],
]) ?>

<?= $form->field($model, 'answersRecreationCoef')->widget(SliderInput::classname(), [
    'clientOptions' => [
        'min' => 1,
        'max' => 10,
    ],
]) ?>

<?= $form->field($model, 'answersGameCoef')->widget(SliderInput::classname(), [
    'clientOptions' => [
        'min' => 1,
        'max' => 10,
    ],
]) ?>

<?= $form->field($model, 'answersEducationalCoef')->widget(SliderInput::classname(), [
    'clientOptions' => [
        'min' => 1,
        'max' => 10,
    ],
]) ?>

<div class="form-group">
    <div>
        <?= Html::submitButton('Перейти к выбору варианта', ['class' => 'btn btn-success', 'name' => 'decision-button']) ?>
    </div>
</div>


<?php ActiveForm::end() ?>
