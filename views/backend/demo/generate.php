<?php

use app\facades\TerritoryFacade;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\forms\demo\GenerateByParamsForm $model */
/** @var string $data */

?>

<div class="object-work-form">

    <?php $form = ActiveForm::begin(['id' => 'generate-form']); ?>

    <div class="row-block" style="display: flex; justify-content: space-between;">

        <div class="float-counter">
            <label class="row-label">Приоритет рекреационных МАФ</label>
            <?= $form->field($model, 'recreation', ['options' => ['style' => 'margin-bottom: 5px; margin-top: 10px']])
                ->textInput(['type' => 'range', 'step' => 0.1, 'min' => 0.1, 'max' => 0.9, 'value' => $model->recreation == 0 ? 0.1 : $model->recreation, 'data-index' => 1])
                ->label(false)
            ?>
            <div style="text-align: center; max-width: 100%">
                <span id="rangeValue1"><?= $model->recreation == 0 ? 0.1 : $model->recreation ?></span>
            </div>
        </div>
        <div class="float-counter">
            <label class="row-label">Приоритет спортивных МАФ</label>
            <?= $form->field($model, 'sport', ['options' => ['style' => 'margin-bottom: 5px; margin-top: 10px']])
                ->textInput(['type' => 'range', 'step' => 0.1, 'min' => 0.1, 'max' => 0.9, 'value' => $model->sport == 0 ? 0.1 : $model->sport, 'data-index' => 2])
                ->label(false)
            ?>
            <div style="text-align: center; max-width: 100%">
                <span id="rangeValue2"><?= $model->sport == 0 ? 0.1 : $model->sport ?></span>
            </div>
        </div>
        <div class="float-counter">
            <label class="row-label">Приоритет развивающих МАФ</label>
            <?= $form->field($model, 'education', ['options' => ['style' => 'margin-bottom: 5px; margin-top: 10px']])
                ->textInput(['type' => 'range', 'step' => 0.1, 'min' => 0.1, 'max' => 0.9, 'value' => $model->education == 0 ? 0.1 : $model->education, 'data-index' => 3])
                ->label(false)
            ?>
            <div style="text-align: center; max-width: 100%">
                <span id="rangeValue3"><?= $model->education == 0 ? 0.1 : $model->education ?></span>
            </div>
        </div>
        <div class="float-counter">
            <label class="row-label">Приоритет игровых МАФ</label>
            <?= $form->field($model, 'game', ['options' => ['style' => 'margin-bottom: 5px; margin-top: 10px']])
                ->textInput(['type' => 'range', 'step' => 0.1, 'min' => 0.1, 'max' => 0.9, 'value' => $model->game == 0 ? 0.1 : $model->game, 'data-index' => 4])
                ->label(false)
            ?>
            <div style="text-align: center; max-width: 100%">
                <span id="rangeValue4"><?= $model->game == 0 ? 0.1 : $model->game ?></span>
            </div>
        </div>

        <div class="float-counter">
            <label class="row-label">Тип генерации</label>
            <?= $form->field($model, 'addGenerateType')->dropDownList([
                TerritoryFacade::OPTIONS_DEFAULT => 'Стандартная генерация',
                TerritoryFacade::OPTIONS_BUDGET_ECONOMY => 'Эконом-генерация',
            ])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сгенерировать', ['class' => 'btn btn-success', 'style' => 'width: 100%;']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="result">
        <?= $data ?>
    </div>

</div>


<style>
    .row-label {
        margin-bottom: 5px;
    }

    .row-block {
        margin-top: 25px;
        margin-bottom: -5px;
        padding: 10px;
        background-color: whitesmoke;
        border-radius: 10px 10px 0 0;
    }

    .row-block:before {
        display: none;
        content: "";
    }

    .row-block:after {
        display: none;
        content: "";
    }

    .float-counter {
        margin-bottom: 10px;
    }
</style>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rangeInputs = document.querySelectorAll('input[type="range"]');

        rangeInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                const value = this.value;
                const spanId = 'rangeValue' + this.dataset.index;
                const span = document.getElementById(spanId);

                if (span) {
                    span.textContent = value;
                }
            });
        });
    });
</script>