<?php

use yii\helpers\Url;

$this->registerJsFile(Url::to('@web/js/points-algorithm.js'), ['depends' => [\yii\web\JqueryAsset::class]]);

?>

<canvas id="clusterCanvas" width="600" height="800"></canvas>
<div></div>
<div class="service">
    <div>
        <h5>Для того, чтобы установить точку - выберите цвет и кликните на карту</h5>
        <br>
        <label>
            <input type="radio" name="color" value="0" checked> Красная
        </label>
        <label>
            <input type="radio" name="color" value="1"> Зеленая
        </label>
        <label>
            <input type="radio" name="color" value="2"> Синяя
        </label>
        <button onclick="clearCanvas()" style="margin-left: 10px;">Очистить карту</button>
    </div>

    <div style="display: flex; align-items: center; margin-top: 20px">
        <label>X: <input type="text" id="coordX" readonly style="max-width: 90px; margin-right: 10px"></label>
        <label>Y: <input type="text" id="coordY" readonly style="max-width: 90px"></label>
        <button onclick="drawClusters()" style="margin-left: 10px;">Построить кластеры</button>
    </div>
</div>

<style>
    canvas {
        border: 1px solid #ccc;
        background-size: revert;
        border-radius: 5px;
    }

    .service {
        max-width: 600px;
        border: 1px solid gray;
        border-radius: 5px;
        background-color: white;
        padding: 20px;
        display: inline-block;
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
