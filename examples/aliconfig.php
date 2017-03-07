<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:19
 * @description:
 */

// 一下配置均为本人的沙箱环境，贡献出来，大家测试

// 个人沙箱帐号：
/*商家账号   naacvg9185@sandbox.com
商户UID   2088102169252684
登录密码   111111*/

/*买家账号    aaqlmq0729@sandbox.com
登录密码    111111
支付密码    111111
用户名称    沙箱环境
证件类型    身份证(IDENTITY_CARD)
证件号码    43982819131125559X*/


return [
    'use_sandbox'               => true,// 是否使用沙盒模式

    'partner'                   => '2088102169252684',
    'app_id'                    => '2016073100130857',
    'sign_type'                 => 'RSA',// RSA  RSA2
    'ali_public_key'            => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'alipay_public_key_rsa.pem',
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
    'notify_url'                => 'http://api-demo.yimishiji.com/v1/notify/ali',
    'return_url'                => 'https://helei112g.github.io/',

    'return_raw'                => false,// 在处理回调时，是否直接返回原始数据，默认为false
];
