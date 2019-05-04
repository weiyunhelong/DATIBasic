<?php

namespace app\models;

use Yii;


class Chance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','matchid','number'], 'integer'],
            [['openid','topenid'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'openid' => 'openid',
            'topenid' => 'topenid',
            'matchid' => 'matchid',
            'number' => 'number',
        ];        
    }

    public function getSuject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subjectid']);
    }
}
