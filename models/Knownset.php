<?php

namespace app\models;

use Yii;


class Knownset extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'knownset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','categoryid','isdifficult','create_at', 'update_at'], 'integer'],
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
            'categoryid' => 'categoryid',
            'name' => '集合名称',            
            'isdifficult' => 'isdifficult',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];        
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'categoryid']);
    }
}
