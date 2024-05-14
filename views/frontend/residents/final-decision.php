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
        <?= Html::submitButton('Перейти к выбору варианта', ['class' => 'btn btn-success', 'name' => 'decision-button']) ?>
    </div>
</div>


<?php ActiveForm::end() ?>
