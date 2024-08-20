<?php

use yii\helpers\Url;

$this->registerJsFile(Url::to('@web/js/points-algorithm.js'), ['depends' => [\yii\web\JqueryAsset::class]]);

?>

<canvas id="clusterCanvas" width="600" height="800"></canvas>
<div></div>
<div class="service">
    <h5>Для того, чтобы установить точку - выберите цвет и кликните на карту</h5>
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div style="width: 55%; align-items: center; display: flex; justify-content: space-between;">
            <div class="radio">
                <input class="custom-radio red-radio" type="radio" id="color-1" name="color" value="0">
                <label for="color-1">Красная</label>
            </div>
            <div class="radio">
                <input class="custom-radio green-radio" type="radio" id="color-2" name="color" value="1">
                <label for="color-2">Зеленая</label>
            </div>
            <div class="radio">
                <input class="custom-radio blue-radio" type="radio" id="color-3" name="color" value="2">
                <label for="color-3">Синяя</label>
            </div>
        </div>
        <div>
            <button onclick="clearCanvas()" class="clear">Очистить карту</button>
        </div>
    </div>

    <div style="display: flex; align-items: center; margin-top: 10px; justify-content: space-between;">
        <div style="width: 55%; display: flex; justify-content: space-between">
            <div style="width: 50%; display: flex;"><span style="margin-right: 10px; min-width: 10%">X: </span><input type="text" id="coordX" readonly style="width: 75%;"></div>
            <div style="width: 50%; display: flex; margin-right: -14px;"><span style="margin-right: 10px; min-width: 10%">Y: </span><input type="text" id="coordY" readonly style="width: 75%;"></div>
        </div>
        <div>
        <button onclick="drawClusters()" style="margin-left: 10px;" class="clusters">Построить кластеры</button>
        </div>
    </div>
</div>

<style>
    canvas {
        border: 1px solid #ccc;
        background-size: revert;
        border-radius: 5px;
    }

    h5 {
        margin-bottom: 30px;
    }

    input {
        border-radius: 5px;
        border: 0;
        background-color: white;
    }

    .service {
         max-width: 600px;
         border: 1px solid gray;
         border-radius: 5px;
         background-color: #EFEFEF;
         padding: 20px;
         display: inline-block;
     }

    .clear {
        margin-left: 10px;
        border-radius: 5px;
        border: 0;
        padding: 5px 10px 5px 10px;
        background-color: #000000;
        color: white;
    }

    .clear:hover {
        background-color: #0c0c0c;
    }

    .clear:active {
        background-color: #151515;
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);
    }

    .clusters {
        margin-left: 10px;
        border-radius: 5px;
        border: 0;
        padding: 5px 10px 5px 10px;
        background-color: rgba(0, 196, 55, 0.75);
        color: black;
    }

    .clusters:hover {
        background-color: rgba(0, 147, 40, 0.75);
    }

    .clusters:active {
        background-color: rgba(0, 147, 40, 0.75);
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);
    }

    .custom-radio {
        position: absolute;
        z-index: -1;
        opacity: 0;
    }

    /* для элемента label связанного с .custom-radio */
    .custom-radio+label {
        display: inline-flex;
        align-items: center;
        user-select: none;
    }

    /* создание в label псевдоэлемента  before со следующими стилями */
    .custom-radio+label::before {
        content: '';
        display: inline-block;
        width: 1em;
        height: 1em;
        flex-shrink: 0;
        flex-grow: 0;
        border: 1px solid #adb5bd;
        border-radius: 50%;
        margin-right: 0.5em;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 50% 50%;
        background-color: white;
    }

    /* стили при наведении курсора на радио */
    .custom-radio:not(:disabled):not(:checked)+label:hover::before {
        border-color: #b3d7ff;
    }

    /* стили для активной радиокнопки (при нажатии на неё) */
    .custom-radio:not(:disabled):active+label::before {
        background-color: #b3d7ff;
        border-color: #b3d7ff;
    }

    /* стили для радиокнопки, находящейся в фокусе */
    .custom-radio:focus+label::before {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* стили для радиокнопки, находящейся в фокусе и не находящейся в состоянии checked */
    .custom-radio:focus:not(:checked)+label::before {
        border-color: #80bdff;
    }

    /* стили для радиокнопки, находящейся в состоянии checked */
    .custom-radio:checked+label::before {
        border-color: #0b76ef;
        background-color: #0b76ef;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }

    /* стили для радиокнопки, находящейся в состоянии disabled */
    .custom-radio:disabled+label::before {
        background-color: #e9ecef;
    }

    .red-radio:checked+label::before {
        border-color: #ef0b0b;
        background-color: #ef0b0b;
    }

    .blue-radio:checked+label::before {
        border-color: #0b76ef;
        background-color: #0b76ef;
    }

    .green-radio:checked+label::before {
        border-color: #10c40a;
        background-color: #0ea403;
    }
</style>


<script>
    const canvas = document.getElementById('clusterCanvas');
    const ctx = canvas.getContext('2d');
    const backgroundImage = new Image();

    // Укажите путь к вашему изображению
    backgroundImage.src = '<?= Yii::$app->request->baseUrl ?>/background.png';

    backgroundImage.onload = () => {
        ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
    };

    // Обработчик клика по канвасу
    canvas.addEventListener('click', (event) => {
        const rect = canvas.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        document.getElementById('coordX').value = x;
        document.getElementById('coordY').value = y;

        drawPoint(x, y);
    });

    function drawPoint(x, y) {
        const color = document.querySelector('input[name="color"]:checked').value;
        let colorCode = '';
        switch (color) {
            case '0':
                colorCode = 'rgb(200, 0, 0)';
                break;
            case '1':
                colorCode = 'rgb(4, 183, 11)';
                break;
            case '2':
                colorCode = 'rgb(0, 0, 200)';
                break;
            default:
                colorCode = 'white';
        }
        ctx.fillStyle = colorCode;
        ctx.beginPath();
        ctx.arc(x, y, 5, 0, Math.PI * 2);
        ctx.fill();
        addPoint(x, y, color);
    }

    function drawClusters() {
        // Ваша логика для построения кластеров
        console.log('Построение кластеров...');
    }
</script>
