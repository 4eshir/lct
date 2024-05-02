<?php

namespace app\models\forms;

use yii\base\Model;

class QuestionForm extends Model
{
    public $userId;
    public $answerAge;
    public $answersSportCoef;
    public $answersRecreationCoef;
    public $answersGameCoef;
    public $answersEducationalCoef;

    public function rules()
    {
        return [
            [['answersSportCoef', 'answersRecreationCoef', 'answersGameCoef', 'answersEducationalCoef', 'userId', 'answerAge'], 'required'],
            [['answersSportCoef', 'answersRecreationCoef', 'answersGameCoef', 'answersEducationalCoef', 'userId', 'answerAge'], 'integer'],
        ];
    }

    public function save()
    {

    }
}