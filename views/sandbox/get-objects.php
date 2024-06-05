<?php

/** @var yii\web\View $this */
/** @var app\models\work\AgesWeightWork $model */
/** @var yii\widgets\ActiveForm $form */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="main-block-api">
    <?= Html::a('Вернуться к документации', ['/api/doc']) ?><br>
    <div class="description-block-query">
        <p>URL: <b>/api/get-objects</b></p>
        <p><b>Параметры</b></p>
        <span class="param-name">type</span>: тип объекта<br>
        <ul>
            <li>1 - Рекреационный</li>
            <li>2 - Спортивный</li>
            <li>3 - Развивающий</li>
            <li>4 - Игровой</li>
        </ul>
        <span class="param-name">minCost</span>: мин. цена<br>
        <ul>
            <li>Целое число</li>
            <li>Не меньше 0</li>
        </ul>
        <span class="param-name">maxCost</span>: макс. цена<br>
        <ul>
            <li>Целое число</li>
            <li>Не меньше minCost</li>
        </ul>
        <span class="param-name">style</span>: стиль<br>
        <ul>
            <li>Строка</li>
            <li>Свободная форма</li>
        </ul>
    </div>

    <?php $form = ActiveForm::begin(['id' => 'get-objects-form']); ?>

    <?= $form->field($model, 'type')->dropDownList([
        1 => '1 - Рекреационный',
        2 => '2 - Спортивный',
        3 => '3 - Развивающий',
        4 => '4 - Игровой',
    ], ['prompt' => '']) ?>

    <?= $form->field($model, 'minCost')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'maxCost')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'style')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Выполнить запрос', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="result-data">

    </div>

</div>

<style>
    .main-block-api {
        background-color: white;
        border-radius: 5px;
        margin-top: 10px;
        padding: 20px;
    }

    .description-block-query {
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #e0e0e0;
        border-radius: 5px;
    }

    .param-name {
        font-weight: 500;
    }

    .result-data {
        background-color: #dcffc8;
        border-radius: 5px;
        margin-top: 10px;
        padding: 20px;
        display: none;
    }
</style>

<?php
$this->registerJs(
    "$(function(){
        $('form').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {
                    let jsonData = JSON.parse(response);
                    jsonData = JSON.stringify(jsonData, null, 2);
                    $('.result-data').show();
                    $('.result-data').html('<p><b>Результат выполнения запроса</b></p><pre>' + jsonData + '</pre>');
                }
            });
        });
    });"
)
?>