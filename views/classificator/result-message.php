<?php

/** @var $result */
/** @var $type */

?>

<div class="container-next">
    <h2 class="result-title <?= $type ?>"><?= $result ?></h2>
    <p class="result-description">Если у Вас есть вопросы по работе сервиса - свяжитесь с нами любым удобным способом:</p>
    <ul>
        <li><b>E-mail:</b> test@test.ru</li>
        <li><b>Телефон:</b> 12-34-56</li>
    </ul>
</div>


<style>
    .container-next {
        max-width: 600px;
        margin: auto;
        margin-top: 20px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .result-title {

        font-size: 24px;
        margin-bottom: 10px;

        padding-bottom: 10px;
    }

    .good {
        color: #4CAF50; /* Цвет заголовка */
        border-bottom: 2px solid #4CAF50; /* Подчеркивание заголовка */
    }

    .bad {
        color: #af4c4c; /* Цвет заголовка */
        border-bottom: 2px solid #af4c4c; /* Подчеркивание заголовка */
    }

    .special {
        color: #4c51af; /* Цвет заголовка */
        border-bottom: 2px solid #4c51af; /* Подчеркивание заголовка */
    }

    .result-description {
        font-size: 16px;
        line-height: 1.5;
        margin-top: 10px;
    }
</style>