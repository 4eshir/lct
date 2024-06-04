<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "territory".
 *
 * @property int $id
 * @property int|null $length
 * @property int|null $width
 * @property string|null $name
 *
 * @property AgesWeightChangeable[] $agesWeightChangeables
 * @property Arrangement[] $arrangements
 * @property PeopleTerritory[] $peopleTerritories
 * @property Questionnaire[] $questionnaires
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
            'length' => 'Length',
            'width' => 'Width',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[AgesWeightChangeables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgesWeightChangeables()
    {
        return $this->hasMany(AgesWeightChangeable::class, ['territory_id' => 'id']);
    }

    /**
     * Gets query for [[Arrangements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArrangements()
    {
        return $this->hasMany(Arrangement::class, ['territory_id' => 'id']);
    }

    /**
     * Gets query for [[PeopleTerritories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleTerritories()
    {
        return $this->hasMany(PeopleTerritory::class, ['territory_id' => 'id']);
    }

    /**
     * Gets query for [[Questionnaires]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionnaires()
    {
        return $this->hasMany(Questionnaire::class, ['territory_id' => 'id']);
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
