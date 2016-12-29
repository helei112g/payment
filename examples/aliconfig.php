<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:19
 * @description:
 */

return [
    // 老版本参数，当使用新版本时，不需要传入
    'partner'   => '',// 请填写自己的支付宝账号信息
    'md5_key'   => 'xxxxxx',// 此密码无效，请填写自己对应设置的值
    // 转款接口，必须配置以下两项
    'account'   => 'xxxxx@126.com',
    'account_name' => 'xxxxx',
    'sign_type' => 'RSA',// 默认方式    目前支持:RSA   MD5`

    // 如果没有设置以下内容，则默认使用老版本
    // 支付宝2.0 接口  如果使用支付宝 新版 接口，请设置该参数，并且必须为 1.0。否则将默认使用支付宝老版接口
    'ali_version'   => '1.0',// 调用的接口版本，固定为：1.0
    'app_id'        => '2016073100130857',// 支付宝分配给开发者的应用ID
    'use_sandbox'   => true,//  新版支付，支持沙箱调试
    'ali_public_key'    => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'alipay_public_key.pem',// 支付宝新版本，每个应用对应的公钥都不一样了

    // 新版与老版支付  共同参数，
    'rsa_private_key'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rsa_private_key.pem',
    "notify_url"	    => 'https://helei112g.github.io/',
    "return_url"	    => 'https://helei112g.github.io/',// 我的博客地址
    "time_expire"	    => '15',// 取值为分钟
];