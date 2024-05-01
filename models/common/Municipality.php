<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "municipality".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $sport_tendency
 * @property int|null $game_tendency
 * @property int|null $education_tendency
 * @property int|null $recreation_tendency
 *
 * @property Ages[] $ages
 */
class Municipality extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'municipality';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sport_tendency', 'game_tendency', 'education_tendency', 'recreation_tendency'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'sport_tendency' => 'Sport Tendency',
            'game_tendency' => 'Game Tendency',
            'education_tendency' => 'Education Tendency',
            'recreation_tendency' => 'Recreation Tendency',
        ];
    }

    /**
     * Gets query for [[Ages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAges()
    {
        return $this->hasMany(Ages::class, ['municipality_id' => 'id']);
    }
}
