<?php

namespace app\models;

use Yii;


class Subject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','create_at'], 'integer'],
            [['name'], 'string', 'max' => 500],
            [['name'],'safe'],//一定要加，不然搜索框出不来
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
