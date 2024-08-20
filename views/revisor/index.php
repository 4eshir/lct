<?php

use yii\helpers\Url; ?>

<style>
    h1 {
        color: white;
        font-family: "Nunito", sans-serif;
        margin-bottom: 25px;
    }

    .fancy-list {
        list-style-type: none; /* Убираем маркеры */
        padding: 0;
        margin: 0;
    }

    .fancy-list li {
        background: #fff;
        border: 2px solid #8860D0;
        border-radius: 8px;
        margin: 10px 0;
        padding: 15px;
        transition: transform 0.2s, box-shadow 0.2s; /* Плавные переходы */
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .fancy-list li:hover {
        transform: scale(1.05); /* Увеличение при наведении */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Тень при наведении */
    }

    .badge {
        background-color: #8860D0;
        color: white;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 5px;
        transition: background-color 0.3s;
        min-width: 25%;
        font-size: 16px;
    }

    .badge:hover {
        background-color: #6430c4;
        text-decoration: none;
        color: white;
    }

    .badge:active {
        background-color: #401d80;
        color: white;
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);
    }

</style>

<br>
<h1>Оцените изменения в городе!</h1>
<ul class="fancy-list">
    <li>
        <span>Опрос "Довольны ли вы детской площадкой по ул. Ленина, 29?"</span>
        <a href="<?= Url::to(['/revisor/survey']) ?>" class="badge">Принять участие</a>
    </li>
    <li>
        <span>Опрос "Качество предоставляемых жилищно-коммунальных услуг в Кировском районе"</span>
        <a href="<?= Url::to(['/revisor/survey']) ?>" class="badge">Принять участие</a>
    </li>
    <li>
        <span>Опрос "Экологическая обстановка Казани"</span>
        <a href="<?= Url::to(['/revisor/survey']) ?>" class="badge">Принять участие</a>
    </li>
    <li>
        <span>Опрос "Пространство для отдыха Советского района"</span>
        <a href="<?= Url::to(['/revisor/survey']) ?>" class="badge">Принять участие</a>
    </li>
    <li>
        <span>Опрос "Довольны ли вы системой опросов"?</span>
        <a href="<?= Url::to(['/revisor/survey']) ?>" class="badge">Принять участие</a>
    </li>
</ul>
