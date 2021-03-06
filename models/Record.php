<?php

namespace app\models;

use Yii;


class Record extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','tid','rightnum','wrongnum','create_at'], 'integer'],
            [['openid','ids'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'openid' => 'Openid',            
            'tid' => 'Tid',    
            'ids' => 'ids',  
            'rightnum' => '答对个数', 
            'wrongnum' => '答错个数',  
            'create_at' => '创建时间'
        ];        
    }

    public function getTiku()
    {
        return $this->hasOne(Tiku::className(), ['id' => 'tid']);
    }

    public function getWxuser()
    {
        return $this->hasOne(WechatUser::className(), ['openid' => 'openid']);
    }
}
