<?php

/** @var $model int[][][] */
/** @var $names string[] */
/** @var $descs string[] */

use app\models\work\TemplateBlockWork;
use yii\helpers\Url;

$this->title = 'Шаблоны';
$this->params['breadcrumbs'][] = [
    'label' => 'Администрация',
    'url' => ['/frontend/administration/index'],
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div id="carouselExampleCaptions" class="carousel slide">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
            <?php

            $activeFlag = false;

            for ($c = 0; $c < count($model); $c++) {
                echo !$activeFlag ? '<div class="carousel-item active">' : '<div class="carousel-item">';
                $activeFlag = true;
                echo '<div class="carousel-caption d-none d-md-block">';
                echo '<div class="template-wrapper">';
                echo '<div class="overlay"></div>';
                echo '<div class="button-container">';
                echo '<button class="btn block-btn">Попробовать шаблон</button>';
                echo '</div>';
                for ($i = 0; $i < 20; $i++) {
                    echo '<div class="row">';
                    for ($j = 0; $j < 20; $j++) {
                        $classname = 'cell-default';
                        if ($model[$c][$i][$j] == 1) $classname = 'cell-recreation';
                        if ($model[$c][$i][$j] == 2) $classname = 'cell-sport';
                        if ($model[$c][$i][$j] == 3) $classname = 'cell-education';
                        if ($model[$c][$i][$j] == 4) $classname = 'cell-game';
                        if ($model[$c][$i][$j] == -1) $classname = 'cell-cancel';

                        echo '<div class="cell '.$classname.'"></div>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                echo '<h4>'.$names[$c].'</h4>';
                echo '<h6>'.$descs[$c].'</h6>';
                echo '</div>';
                echo '</div>';
            }
            ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Предыдущий</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Следующий</span>
    </button>
</div>

<div class="template-wrapper-desc">
    <div class="cell-t cell-recreation"></div><span> - зона рекреационных объектов</span><br>
    <div class="cell-t cell-sport"></div><span> - зона спортивных объектов</span><br>
    <div class="cell-t cell-education"></div><span> - зона развивающих объектов</span><br>
    <div class="cell-t cell-game"></div><span> - зона игровых объектов</span><br>
    <div class="cell-t cell-cancel"></div><span> - зона, в которой нельзя строить</span><br>
    <div class="cell-t cell-default"></div><span> - свободная зона</span><br>
</div>


<div class="result" id="resultId">

</div>



<style>
    .carousel-item {
        width: 100%;
        height: 850px;
        /*background-color: #8860cf;*/
        border-radius: 20px;

        border: 0.1px solid white;
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 15px #644bb1;
    }

    .template-wrapper {
        padding: 30px;
        background-color: white;
        border-radius: 15px;
        width: 100%;
        margin: 10px;
    }

    .template-wrapper-desc {
        padding: 30px;
        background-color: white;
        border-radius: 15px;
        margin: 10px 0 10px 0;
    }

    .cell {
        width: 26px;
        height: 26px;
        border-radius: 5px;
        margin: 3px;
    }

    .cell-t {
        width: 26px;
        height: 26px;
        border-radius: 5px;
        margin: 3px;

        display: inline-block;
        vertical-align: middle;
    }

    .cell-t span {
        display: inline-block;
        vertical-align: middle;
        margin-left: 5px; /* Добавляем отступ слева, если нужно */
    }

    .cell-default {
        border: 1px solid #6c6c6c;
        background-color: #b8b8b8;
    }

    .cell-sport {
        border: 1px solid #3b7233;
        background-color: #b7e2ae;
    }

    .cell-recreation {
        border: 1px solid #2b6a92;
        background-color: #9dcae5;
    }

    .cell-education {
        border: 1px solid #823f89;
        background-color: #d4a4de;
    }

    .cell-game {
        border: 1px solid #74762f;
        background-color: #faf0ae;
    }

    .cell-cancel {
        border: 1px solid #842a2a;
        background-color: #d59191;
    }

    .row {
        display: flex;
        justify-content: center;
    }

    .row .cell {
        width: 26px !important;
        height: 26px !important;
    }

    .overlay {
        display: none;
        position: absolute;
        top: 0;
        left: 10px;
        width: 100%;
        height: 700px;
        margin-top: 30px;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
        border-radius: 14px;
    }

    .button-container {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .block-btn {
        background-color: white;
        border: 1.5px solid #361967;
        font-size: 22px;
        padding: 15px 25px 15px 25px;
        border-radius: 10px;
        font-family: "Nunito", sans-serif;
        font-weight: 700;
        color: #361967;
    }

    .block-btn:hover {
        background-color: #4c2c81;
        border: 1px solid #361967;
        color: white;
    }

    .result {
        margin-top: 50px;
        height: 700px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const templateWrappers = document.querySelectorAll('.template-wrapper');

        templateWrappers.forEach((templateWrapper, index) => {
            const overlay = document.createElement('div');
            const buttonContainer = document.createElement('div');
            const button = document.createElement('button');
            const loadingIcon = document.createElement('div');

            overlay.classList.add('overlay');
            templateWrapper.appendChild(overlay);

            buttonContainer.classList.add('button-container');
            button.textContent = 'Попробовать шаблон ' + (index + 1);
            button.classList.add('btn', 'block-btn');
            buttonContainer.appendChild(button);
            loadingIcon.classList.add('loading-icon');
            loadingIcon.innerHTML = '<img src="/img/loading.gif" alt="Подождите...">';
            loadingIcon.style.display = 'none';
            buttonContainer.appendChild(loadingIcon);

            templateWrapper.appendChild(buttonContainer);

            templateWrapper.addEventListener('mouseenter', function () {
                overlay.style.display = 'block';
                buttonContainer.style.display = 'block';
            });

            templateWrapper.addEventListener('mouseleave', function () {
                overlay.style.display = 'none';
                buttonContainer.style.display = 'none';
            });

            button.addEventListener('click', function () {
                const params = {
                    templateId: index + 1,
                };

                sendAjaxRequest(params, loadingIcon);
            });
        });

        function sendAjaxRequest(params, loadingIcon) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "index.php?r=/backend/demo/generate-template-ajax&tId=" + params['templateId'], true);

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var resultDiv = document.querySelector(".result");
                    resultDiv.textContent = xhr.responseText;
                    scrollToAnchor('resultId');
                } else {
                    console.error("Ошибка при загрузке данных: " + xhr.status);
                }
                loadingIcon.style.display = 'none';
            };

            xhr.onerror = function() {
                console.error("Произошла ошибка при выполнении запроса.");
                loadingIcon.style.display = 'none';
            };

            xhr.send();
            loadingIcon.style.display = 'block';
        }

        function scrollToAnchor(anchorId) {
            const anchor = document.getElementById(anchorId);
            if (anchor) {
                anchor.scrollIntoView({ behavior: 'smooth' }); // плавная прокрутка к якорю
            }
        }
    });
</script>