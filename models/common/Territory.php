<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "territory".
 *
 * @property int $id
 * @property int|null $length
 * @property int|null $width
 *
 * @property RestrictObjectTerritory[] $restrictObjectTerritories
 */
class Territory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'territory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['length', 'width'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'length' => 'Length',
            'width' => 'Width',
        ];
    }

    /**
     * Gets query for [[RestrictObjectTerritories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRestrictObjectTerritories()
    {
        return $this->hasMany(RestrictObjectTerritory::class, ['territory_id' => 'id']);
    }
}
