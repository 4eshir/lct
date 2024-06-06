<?php


use app\models\work\ObjectWork;
use yii\db\BatchQueryResult;

class ObjectAnalytic
{
    const SIMILAR_TYPE_FULL = 1; // поиск максимально похожих объектов
    const SIMILAR_TYPE_QUITE = 2; // поиск достаточно похожих объектов
    const SIMILAR_TYPE_SLIGHTLY = 3; // поиск немного похожих объектов


    public function findSimilarObjects(ObjectWork $targetObject, $type)
    {
        $baseObjects = ObjectWork::find()->where(['object_type_id' => $targetObject->object_type_id])->andWhere(['!=', 'id', $targetObject->id])->each();

        switch ($type) {
            case self::SIMILAR_TYPE_FULL:
                $baseObjects = $this->findSimilarFull($targetObject, $baseObjects);
                break;
            case self::SIMILAR_TYPE_QUITE:
                $baseObjects = $this->findSimilarQuite($targetObject, $baseObjects);
                break;
            case self::SIMILAR_TYPE_SLIGHTLY:
                $baseObjects = $this->findSimilarSlightly($targetObject, $baseObjects);
                break;
            default:
                throw new \yii\base\Exception('Неизвестный тип поиска схожих объектов');
        }

        return $baseObjects;
    }

    /**
     * Функция поиска максимально схожих объектов
     * * Название совпадает на 50%
     * * Разброс по площади +-20%
     * * Разброс по стоимости +-30%
     * * Разброс по времени установки и времени создания +-50%
     * * Стили объектов совпадают
     * @param ObjectWork $targetObject исходный объект
     * @param BatchQueryResult $objects итератор по исходному массиву объектов
     */
    private function findSimilarFull(ObjectWork $targetObject, $objects)
    {
        $result = [];

        foreach ($objects as $object) {
            /** @var ObjectWork $object */
            if (
                $this->checkStringsDiff($targetObject->name, $object->name, 50) &&
                $this->checkNumbersDiff($targetObject->getSquare(), $object->getSquare(), 20) &&
                $this->checkNumbersDiff($targetObject->cost, $object->cost, 30) &&
                $this->checkNumbersDiff($targetObject->install_time, $object->install_time, 50) &&
                $this->checkNumbersDiff($targetObject->created_time, $object->created_time, 50) &&
                $this->checkStringsDiff($targetObject->style, $object->style, 100)
            ) {
                $result[] = $object;
            }
        }

        return $result;
    }

    /**
     * Функция поиска достаточно схожих объектов
     * * Название совпадает на 50%
     * * Разброс по площади +-40%
     * * Разброс по стоимости +-50%
     * * Разброс по времени установки и времени создания +-50%
     * * Стили объектов значения не имеют
     * @param ObjectWork $targetObject исходный объект
     * @param BatchQueryResult $objects итератор по исходному массиву объектов
     */
    private function findSimilarQuite(ObjectWork $targetObject, $objects)
    {
        $result = [];

        foreach ($objects as $object) {
            /** @var ObjectWork $object */
            if (
                $this->checkStringsDiff($targetObject->name, $object->name, 50) &&
                $this->checkNumbersDiff($targetObject->getSquare(), $object->getSquare(), 40) &&
                $this->checkNumbersDiff($targetObject->cost, $object->cost, 50) &&
                $this->checkNumbersDiff($targetObject->install_time, $object->install_time, 50) &&
                $this->checkNumbersDiff($targetObject->created_time, $object->created_time, 50)
            ) {
                $result[] = $object;
            }
        }

        return $result;
    }

    /**
     * Функция поиска немного схожих объектов
     * * Название совпадает на 30%
     * * Разброс по площади +-70%
     * * Разброс по стоимости +-100%
     * * Разброс по времени установки и времени создания значения не имеет
     * * Стили объектов значения не имеют
     * @param ObjectWork $targetObject исходный объект
     * @param BatchQueryResult $objects итератор по исходному массиву объектов
     */
    private function findSimilarSlightly(ObjectWork $targetObject, $objects)
    {
        $result = [];

        foreach ($objects as $object) {
            /** @var ObjectWork $object */
            if (
                $this->checkStringsDiff($targetObject->name, $object->name, 30) &&
                $this->checkNumbersDiff($targetObject->getSquare(), $object->getSquare(), 70) &&
                $this->checkNumbersDiff($targetObject->cost, $object->cost, 100)
            ) {
                $result[] = $object;
            }
        }

        return $result;
    }

    /**
     * Функция сравнения двух строк (алгоритм шинглов)
     * @param string $str1
     * @param string $str2
     * @param int $percent минимальный процент совпадения строк
     * @return bool
     */
    private function checkStringsDiff(string $str1, string $str2, int $percent)
    {
        $shingleCount = 3; // Количество символов в шингле (можно изменить по необходимости)

        $shingles1 = [];
        $shingles2 = [];

        // Создаем шинглы для первой строки
        for ($i = 0; $i <= strlen($str1) - $shingleCount; $i++) {
            $shingles1[] = substr($str1, $i, $shingleCount);
        }

        // Создаем шинглы для второй строки
        for ($i = 0; $i <= strlen($str2) - $shingleCount; $i++) {
            $shingles2[] = substr($str2, $i, $shingleCount);
        }

        // Подсчитываем количество общих шинглов
        $commonShingles = array_intersect($shingles1, $shingles2);
        $diffPercent = (1 - count($commonShingles) / max(count($shingles1), count($shingles2))) * 100;

        // Проверяем, превышено ли указанное различие в процентах
        return $diffPercent > $percent;
    }

    /**
     * Функция сравнения двух чисел
     * @param int $numb1
     * @param int $numb2
     * @param int $percent максимальный процент расхождения чисел
     * @return bool
     */
    private function checkNumbersDiff(int $numb1, int $numb2, int $percent)
    {
        $diff = abs($numb1 - $numb2);
        $percentDiff = $diff / $numb2 * 100;

        return $percentDiff <= $percent;
    }
}