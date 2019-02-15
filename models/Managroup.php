<?php

namespace app\models;

use Yii;


class Managroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'magagroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','tid','knownids','create_at', 'update_at'], 'integer'],
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
            'tid' => 'tid',//推行官id
            'name' => '名称',            
            'knownids' => '知识点',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];        
    }
}
