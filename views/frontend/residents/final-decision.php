<?php

/** @var $model QuestionDecisionForm */

use app\models\forms\QuestionDecisionForm;
use app\models\forms\QuestionForm;
use app\models\work\AgesIntervalWork;
use yii\helpers\Html;
use yii\jui\SliderInput;
use yii\widgets\ActiveForm;
?>


<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'decision')->dropDownList([1 => 'Вариант 1', 2 => 'Вариант 2', 3 => 'Вариант 3']) ?>

<div class="form-group">
    <div>
        <?= Html::submitButton('Подтвердить выбор', ['class' => 'btn btn-success', 'name' => 'decision-button']) ?>
    </div>
</div>

<div class="territories">
    <div class="base-weights">
        <h2>Вариант 1</h2>
        <?= var_dump($model->territoires[0]->getRawMatrix()) ?>
    </div>
    <div class="change-weights">
        <h2>Вариант 2</h2>
        <?= var_dump($model->territoires[1]->getRawMatrix()) ?>
    </div>
    <div class="votes">
        <h2>Вариант 3</h2>
        <?= var_dump($model->territoires[2]->getRawMatrix()) ?>
    </div>
</div>


<?php ActiveForm::end() ?>
