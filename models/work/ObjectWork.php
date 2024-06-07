<?php

namespace app\models\work;

use app\components\arrangement\TerritoryConcept;
use app\models\common\ObjectT;

class ObjectWork extends ObjectT
{
    const TYPE_RECREATION = '1';
    const TYPE_SPORT = '2';
    const TYPE_EDUCATION = '3';
    const TYPE_GAME = '4';

    public $lengthCells = 0;
    public $widthCells = 0;

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'length' => 'Длина (в см)',
            'width' => 'Ширина (в см)',
            'height' => 'Высота (в см)',
            'cost' => 'Стоимость',
            'created_time' => 'Время изготовления',
            'install_time' => 'Время установки',
            'worker_count' => 'Кол-во рабочих для установки',
            'object_type_id' => 'Тип объекта',
            'creator' => 'Производитель',
            'dead_zone_size' => 'Доп. зона безопасности (в см)',
            'style' => 'Стиль',
            'model_path' => 'Путь до 3D модели',
            'article' => 'Артикул',
            'age' => 'Возрастная категория',
            'left_age' => 'Минимальный возраст',
            'right_age' => 'Максимальный возраст',
        ];
    }
    public static function types()
    {
        return [
            self::TYPE_RECREATION,
            self::TYPE_SPORT,
            self::TYPE_EDUCATION,
            self::TYPE_GAME,
        ];
    }

    public function convertDimensionsToCells()
    {
        $this->lengthCells = self::convertDistanceToCells($this->length, TerritoryConcept::STEP);
        $this->widthCells = self::convertDistanceToCells($this->width, TerritoryConcept::STEP);
    }

    public function getSquareCells()
    {
        return $this->lengthCells * $this->widthCells;
    }

    public function getSquare()
    {
        return $this->length * $this->width;
    }

    // функция конвертации обычного расстояния в "ячейки"
    public static function convertDistanceToCells($distance, $step)
    {
        $cells = intdiv($distance, $step);
        return $distance % $step != 0 ? $cells + 1 : $cells;
    }

    public function getAge()
    {
        return $this->left_age . ' - ' . $this->right_age . ' л.';
    }
}