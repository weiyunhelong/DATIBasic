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
        return 'magaggame';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','isyear','isanswer','level','status','create_at', 'update_at'], 'integer'],
            [['name','showname','rule','logo'], 'string', 'max' => 500],
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
            'isyear' => '是否年级分组',
            'isanswer' => '是否显示答案',
            'showname' => '活动名称',
            'logo' => 'LOGO',
            'rule' => '规则',
            'level' => '通过的等级',
            'status' => '是否上线',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];        
    }
}
