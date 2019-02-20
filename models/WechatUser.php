<?php

namespace app\models;

use Yii;


class WechatUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxuser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','gender','chancenum','create_at', 'update_at'], 'integer'],
            [['openid','unionid','nickname', 'avatar','country','province','city'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'openid' => 'OpenId',
            'unionid' => 'UnionId',
            'nickname' => '昵称',
            'avatar' => '头像',
            'gender' => '性别',
            'country' => '国家',
            'province' => '省份',
            'city' => '城市',
            'chancenum' => '挑战次数',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];
    }
}
