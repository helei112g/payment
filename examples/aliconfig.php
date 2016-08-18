<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:19
 * @description:
 */

return [
    'partner'   => '2088xxxxx',// 请填写自己的支付宝账号信息
    'md5_key'   => 'xxxxxx',// 此密码无效，请填写自己对应设置的值
    'rsa_private_key'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rsa_private_key.pem',
    "notify_url"	=> 'http://test.helei.com/pay-notify.html',
    "return_url"	=> 'http://test.helei.com/return-url.html',
    "time_expire"	=> '14',

    // 转款接口，必须配置以下两项
    'account'   => 'xxxxx@126.com',
    'account_name' => 'xxxxx',
    'sign_type' => 'RSA',// 默认方式    目前支持:RSA   MD5`
];