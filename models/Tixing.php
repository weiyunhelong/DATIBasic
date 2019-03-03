<?php

namespace app\models;

use Yii;


class Tixing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tixing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','create_at','update_at'], 'integer'],
            [['name'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'name' => '名称',        
            'create_at' => '创建时间',
            'update_at' => '更新时间'
        ];        
    }

}
