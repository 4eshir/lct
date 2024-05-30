<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "arrangement".
 *
 * @property int $id
 * @property string|null $model
 * @property int|null $user_id
 * @property string|null $generate_type base - на базовых весах, change - на измененных весах, self - на основе голосов пользователя, manual - собрано вручную
 * @property string|null $datetime
 */
class Arrangement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arrangement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model'], 'string'],
            [['user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['generate_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Model',
            'user_id' => 'User ID',
            'generate_type' => 'Generate Type',
            'datetime' => 'Datetime',
        ];
    }
}
