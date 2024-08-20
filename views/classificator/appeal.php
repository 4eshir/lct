<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Appeal $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="appeal-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="main-form-block">
        <?= $form->field($model, 'message')->textarea(['rows' => 5])->label('Опишите Вашу проблему или предложение в поле ниже') ?>

        <div class="form-group btn-block">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<style>
    .main-form-block {
        max-width: 600px;
        margin: auto;
        margin-top: 20px;
        padding: 10px 10px 1px 10px;
        border-radius: 8px;
        background-color: #EFEFEF
    }

</style>