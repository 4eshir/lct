<?php

use app\components\arrangement\TerritoryConcept;
use app\models\forms\demo\GenerateAnalogForm;
use app\models\work\TerritoryWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var GenerateAnalogForm $model */
/** @var string $data */
?>

<div class="object-work-form">

    <?php $form = ActiveForm::begin(['id' => 'generate-form']); ?>

    <?= $form->field($model, 'analogTerritoryId')->dropDownList(TerritoryWork::getFixedTerritories(), ['prompt' => '--']) ?>

    <?= $form->field($model, 'fullness')->dropDownList([
        TerritoryConcept::TYPE_FULLNESS_ORIGINAL => 'Оригинальная',
        TerritoryConcept::TYPE_FULLNESS_MAX => 'Максимум',
        TerritoryConcept::TYPE_FULLNESS_MID => 'Средняя',
        TerritoryConcept::TYPE_FULLNESS_MIN => 'Низкая',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сгенерировать', ['class' => 'btn btn-success', 'style' => 'width: 100%;']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="pre-data">

    </div>

    <div class="result">
        <?= $data ?>
    </div>

</div>


<?php
$script = <<< JS
    $(document).ready(function(){
        var territoryId = $('#generateanalogform-analogterritoryid').val();
        $.ajax({
            url: '/index.php?r=backend/demo/render-arrangement-ajax',
            type: 'GET',
            data: {territoryId: territoryId},
            success: function(response){
                $('.pre-data').html(response);
            },
            error: function(xhr){
                console.log('Ошибка ' + xhr.status);
            }
        });
    });

    $('#generateanalogform-analogterritoryid').change(function(){
        var territoryId = $(this).val();
        $.ajax({
            url: '/index.php?r=backend/demo/render-arrangement-ajax',
            type: 'GET',
            data: {territoryId: territoryId},
            success: function(response){
                $('.pre-data').html(response);
            },
            error: function(xhr){
                console.log('Ошибка ' + xhr.status);
            }
        });
    });
JS;

// Регистрируем скрипт
$this->registerJs($script);
?>

