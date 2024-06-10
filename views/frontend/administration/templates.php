<?php

/** @var $model int[][][] */
/** @var $names string[] */
/** @var $descs string[] */

use app\models\work\TemplateBlockWork;

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





<style>
    .carousel-item {
        margin-top: 30px;
        width: 100%;
        height: 850px;
        background-color: #8860cf;
        border-radius: 20px;
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
        margin: 10px;
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
        background-color: #d4de9a;
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
</style>