<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:19
 * @description:
 */

return [
    'use_sandbox'               => false,// 是否使用沙盒模式

    'partner'                   => '2088xxxxxxx',
    'app_id'                    => '2016xxxxxxxx',
    'account'                   => 'xxxxxxx@163.com',
    'account_name'              => '何磊',
    'sign_type'                 => 'RSA',// RSA  RSA2
    'ali_public_key'            => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'alipay_public_key.pem',
    'rsa_private_key'           => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rsa_private_key.pem',
    'limit_pay'      => [
        //'balance',// 余额
        //'moneyFund',// 余额宝
        //'debitCardExpress',// 	借记卡快捷
        'creditCard',//信用卡
        //'creditCardExpress',// 信用卡快捷
        //'creditCardCartoon',//信用卡卡通
        //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
    ],// 用户不可用指定渠道支付当有多个渠道时用“,”分隔

    // 与业务相关参数
    'notify_url'                => 'https://helei112g.github.io/',
    'return_url'                => 'https://helei112g.github.io/',

    'return_raw'                => false,// 在处理回调时，是否直接返回原始数据，默认为false
];
