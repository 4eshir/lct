<?php

namespace app\models\work;


use app\models\common\User;

class UserWork extends User
{
    /**
     * Возвращает аутентифицированного пользователя
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getAuthUser()
    {
        return UserWork::find()->where(['auth_flag' => 1])->one();
    }
}