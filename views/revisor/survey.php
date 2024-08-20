<?php

/** @var $model QuestionForm */

use app\models\forms\QuestionForm;
use app\models\work\AgesIntervalWork;
use app\models\work\TerritoryWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\SliderInput;
use yii\widgets\ActiveForm;
?>


<?php $form = ActiveForm::begin(['id' => 'myForm']) ?>

<div class="description-block">
    <div class="description-head"></div>
    <label class="main-label">Голосование за благоустройство придомовой территории</label>
    <br>
    <span class="description">Уважаемый житель города N, ответьте на несколько вопросов, касающихся детской площадки, которая будет располагаться на территории вашего двора</span>
</div>

<div class="question-block">
    <div class="question-head"></div>
    <label class="question-label">Выберите придомовую территорию вашего дома</label>
    <?= $form->field($model, 'territory')->dropDownList(TerritoryWork::GetAllTerritories())->label(false) ?>
</div>

<div class="question-block">
    <div class="question-head"></div>
    <label class="question-label">Выберите возрастную категорию голосующего</label>
    <?= $form->field($model, 'answerAge')->dropDownList(AgesIntervalWork::GetAllIntervals())->label(false) ?>
</div>

<div class="question-block">
    <div class="question-head"></div>
    <label class="question-label">Оцените приоритет <u>спортивных</u> объектов на площадке (от 1 до 10)</label>

    <div class=wrapper>
        <div class="range">
            <input type="range" min="1" max="10" value="0" id="questionform-answerssportcoef" name="QuestionForm[answersSportCoef]"/>
            <div class="value value1">1</div>
        </div>
    </div>

</div>

<div class="question-block">
    <div class="question-head"></div>
    <label class="question-label">Оцените приоритет <u>рекреационных</u> объектов на площадке (от 1 до 10)</label>

    <div class=wrapper>
        <div class="range">
            <input type="range" min="1" max="10" value="1" id="questionform-answersrecreationcoef" name="QuestionForm[answersRecreationCoef]"/>
            <div class="value value2">1</div>
        </div>
    </div>

</div>

<div class="question-block">
    <div class="question-head"></div>
    <label class="question-label">Оцените приоритет <u>игровых</u> объектов на площадке (от 1 до 10)</label>

    <div class=wrapper>
        <div class="range">
            <input type="range" min="1" max="10" value="1" id="questionform-answersgamecoef" name="QuestionForm[answersGameCoef]"/>
            <div class="value value3">1</div>
        </div>
    </div>

</div>

<div class="question-block">
    <div class="question-head"></div>
    <label class="question-label">Оцените приоритет <u>развивающих</u> объектов на площадке (от 1 до 10)</label>

    <div class=wrapper>
        <div class="range">
            <input type="range" min="1" max="10" value="1" id="questionform-answerseducationalcoef" name="QuestionForm[answersEducationalCoef]"/>
            <div class="value value4">1</div>
        </div>
    </div>

</div>

<div class="form-group">
    <div>
        <?= Html::button('Завершить опрос', ['class' => 'btn btn-success btn-custom', 'name' => 'decision-button', 'id' => 'pseudo-submit']) ?>
    </div>
</div>


<?php ActiveForm::end() ?>


<!-- Модальное окно -->
<div id="confirmationModal" style="display:none;">
    <div class="modal-content">
        <img src="<?= Url::to('@web/img/city.png') ?>" style="width: 70%; margin: auto"/>
        <h3>Спасибо за ваше участие в опросе! Вы помогаете своему городу становиться лучше</h3>
        <p class="main-text">Вы получили: <b>12 🔅</b></p>
        <button id="confirmSubmit" class="btn btn-success btn-green">Вернуться к опросам</button>
    </div>
</div>

<style>
    /* Стиль для модального окна */
    #confirmationModal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
        display: none;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 30%;
        border-radius: 10px;
        text-align: center;
    }

    .main-text {
        margin-top: 15px;
        padding: 20px 10px;
        border-radius: 5px;
        background-color: #EFEFEF;
        font-size: 22px;
    }

    .btn-green {
        background-color: rgba(0, 147, 40, 0.75);
    }
</style>

<script>
    document.getElementById('pseudo-submit').addEventListener('click', function (event) {

        // Показываем модальное окно
        document.getElementById('confirmationModal').style.display = 'block';
    });

    // Обработка подтверждения отправки
    document.getElementById('confirmSubmit').onclick = function () {
        document.getElementById('myForm').submit(); // Отправляем форму
    };
</script>