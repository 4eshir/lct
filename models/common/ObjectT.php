<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "object".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $length
 * @property int|null $width
 * @property int|null $height
 * @property float|null $cost
 * @property int|null $created_time Время изготовления в днях
 * @property int|null $install_time Время установки в днях
 * @property int|null $worker_count
 * @property int|null $object_type_id
 * @property string|null $creator
 * @property int|null $dead_zone_size
 *
 * @property ObjectType $objectType
 */
class ObjectT extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['length', 'width', 'height', 'created_time', 'install_time', 'worker_count', 'object_type_id', 'dead_zone_size'], 'integer'],
            [['cost'], 'number'],
            [['name', 'creator'], 'string', 'max' => 256],
            [['object_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectType::class, 'targetAttribute' => ['object_type_id' => 'id']],
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
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'cost' => 'Cost',
            'created_time' => 'Created Time',
            'install_time' => 'Install Time',
            'worker_count' => 'Worker Count',
            'object_type_id' => 'Object Type ID',
            'creator' => 'Creator',
            'dead_zone_size' => 'Dead Zone Size',
        ];
    }

    /**
     * Gets query for [[ObjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectType()
    {
        return $this->hasOne(ObjectType::class, ['id' => 'object_type_id']);
    }
}
