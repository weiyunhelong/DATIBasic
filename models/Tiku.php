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
            [['id','tixingid','knowsetid','showtype','answer','difficult','create_at','update_at'], 'integer'],
            [['knowids','title','imgpath','optionA','optionB','optionC','optionD','optionE','optionF','mark'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'tixingid' => 'tixingid',  
            'knowsetid' => 'knowsetid',  
            'showtype' => 'showtype',     
            'answer' => 'answer',     
            'difficult' => 'difficult',     
            'knowids' => 'knowids',    
            'title' => '题目',   
            'imgpath' => '图片',   
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
        return $this->hasOne(Tixing::className(), ['id' => 'tixingid']);
    }

    public function getKnowset()
    {
        return $this->hasOne(Knowset::className(), ['id' => 'knowsetid']);
    }
}
