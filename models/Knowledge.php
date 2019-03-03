<?php

namespace app\models;

use Yii;


class Knowledge extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'knowledge';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','categoryid','knownsetid','create_at', 'update_at'], 'integer'],
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
            'knownsetid' => 'knownsetid',
            'name' => '知识点名称',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];        
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'categoryid']);
    }

    public function getKnowset()
    {
        return $this->hasOne(Knowset::className(), ['id' => 'knowsetid']);
    }
}
