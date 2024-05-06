<?php

namespace app\models\forms;

use yii\base\Model;

class QuestionDecisionForm extends Model
{
    public $decision;

    public function rules()
    {
        return [
            [['decision'], 'required'],
            [['decision'], 'integer'],
        ];
    }
}