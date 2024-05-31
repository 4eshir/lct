<?php

/** @var $model ConstructorTerritoryForm */

use app\models\forms\ConstructorTerritoryForm;
use app\models\work\ObjectWork;

?>

<div>
    <h3>Настроения жителей</h3>
    <span><b>R:</b> <?= $model->averageMindset[ObjectWork::TYPE_RECREATION] ?>% </span>
    <span><b>S:</b> <?= $model->averageMindset[ObjectWork::TYPE_SPORT] ?>% </span>
    <span><b>E:</b> <?= $model->averageMindset[ObjectWork::TYPE_EDUCATION] ?>% </span>
    <span><b>G:</b> <?= $model->averageMindset[ObjectWork::TYPE_GAME] ?>% </span>
</div>

<div>
    <h3>Жители чаще всего выбирают примерно такую планировку:</h3>
    <span><?php var_dump($model->generatePriorityArrangement()->getRawMatrix()) ?></span>
    <h2>Бюджет на данную планировку: </h2>
    <span><?= $model->generatePriorityArrangement()->calculateBudget(); ?>₽</span>
    <h2>Время изготовления/установки объектов: </h2>
    <span><?= $model->generatePriorityArrangement()->calculateCreatedTime(); ?>д. / <?= $model->generatePriorityArrangement()->calculateInstallationTime(); ?>д.</span>
</div>
