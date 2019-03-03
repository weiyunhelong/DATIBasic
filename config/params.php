<?php

return [
    'adminEmail' => 'admin@example.com',

    'appid' => 'wxfa6e140e888ec2b4',
    'secret' => '013eec22017d1d67a1799a8a63b8d517',

    'wx'=>[
        //  公众号信息
        'mp'=>[
            //  账号基本信息
            'app_id'  => '', // 公众号的appid
            'secret'  => '', // 公众号的秘钥
            'token'   => '', // 接口的token
            'encodingAESKey'=>'',
            'safeMode'=>0,

            //  微信支付
            'payment'=>[
                'mch_id'        =>  '',// 商户ID
                'key'           =>  '',// 商户KEY
                'notify_url'    =>  '',// 支付通知地址
                'cert_path'     => '',// 证书
                'key_path'      => '',// 证书
            ],

            // web授权
            'oauth' => [
                'scopes'   => 'snsapi_userinfo',// 授权范围
                'callback' => '',// 授权回调
            ],
        ],

        //  小程序配置
        'mini'=>[
            //  基本配置
            'app_id'  => 'wxfa6e140e888ec2b4',
            'secret'  => '013eec22017d1d67a1799a8a63b8d517',
            //  微信支付
            'payment' => [
                'mch_id'        => '',
                'key'           => '',
            ],
        ]
        ],
];
