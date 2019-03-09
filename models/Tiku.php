<?php

namespace app\models;

use Yii;


class Tiku extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tiku';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','categoryid','tixingid','knowsetid','showtype','answer','difficult','create_at','update_at'], 'integer'],
            [['knownids','title','imgpath','optionA','optionB','optionC','optionD','optionE','optionF','mark'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'categoryid'=>'categoryid',
            'tixingid' => 'tixingid',  
            'knowsetid' => 'knowsetid',  
            'showtype' => 'showtype',     
            'answer' => 'answer',     
            'difficult' => 'difficult',     
            'knownids' => 'knownids',    
            'title' => '题目',   
            'imgpath' => '',   
            'optionA' => '选项A',  
            'optionB' => '选项B', 
            'optionC' => '选项C',    
            'optionD' => '选项D', 
            'optionE' => '选项E', 
            'optionF' => '选项F', 
            'mark'=>'解析',
            'create_at' => '创建时间',
            'update_at' => '更新时间'
        ];        
    }

    public function getTixing()
    {
        //return $this->hasOne(Tixing::className(), ['id' => 'tixingid']);
    }

    public function getKnowset()
    {
        return $this->hasOne(Knowset::className(), ['id' => 'knowsetid']);
    }
}
