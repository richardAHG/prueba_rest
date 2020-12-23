<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $id
 * @property string $nombre
 * @property int $tipo_id
 * @property int $parent_id
 * @property int|null $estado
 */
class AreaModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'tipo_id', 'parent_id'], 'required'],
            [['tipo_id', 'parent_id', 'estado'], 'integer'],
            [['nombre'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'tipo_id' => 'Tipo ID',
            'parent_id' => 'Parent ID',
            'estado' => 'Estado',
        ];
    }
}
