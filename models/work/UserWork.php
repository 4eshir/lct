<?php

namespace app\models\work;


use app\models\common\User;

class UserWork extends User
{
    const ROLE_CITIZEN = 1;
    const ROLE_ADMINISTRATOR = 2;
    const ROLE_GOD = 3;

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'municipality_id' => 'Муниципалитет',
            'role' => 'Роль в системе',
            'auth_flag' => 'Auth Flag',
        ];
    }

    /**
     * Возвращает аутентифицированного пользователя
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getAuthUser()
    {
        return UserWork::find()->where(['auth_flag' => 1])->one();
    }

    public static function logout()
    {
        $entity = self::getAuthUser();
        $entity->auth_flag = 0;
        $entity->save();
    }
}