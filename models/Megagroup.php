<?php

namespace app\models;

use Yii;


class Megagroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'megagroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','tid','mid','status','create_at', 'update_at'], 'integer'],
            [['name','knownids'], 'string', 'max' => 500],
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
            'mid'=> 'mid',//赛事id
            'name' => '名称',            
            'knownids' => '知识点',
            'status' => '状态值',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];        
    }
}
