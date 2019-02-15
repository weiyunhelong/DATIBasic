<?php

namespace app\models;

use Yii;


class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','subjectid','create_at', 'update_at'], 'integer'],
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
            'subjectid' => 'subjectid',
            'name' => '真实科目',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];        
    }

    public function getSuject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subjectid']);
    }
}
