<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:19
 * @description:
 */

return [
    'partner'           => '',
    'app_id'            => '2016073100130857',
    'account'           => 'xxxxx@126.com',
    'account_name'      => 'xxxxx',
    'use_sandbox'       => true,
    'sign_type'         => 'RSA',
    'ali_public_key'    => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'alipay_public_key.pem',
    'rsa_private_key'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rsa_private_key.pem',

    // 与业务相关参数
    'time_expire'       => '15',
    'notify_url'        => 'https://helei112g.github.io/',
    'return_url'        => 'https://helei112g.github.io/',
];
