<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:19
 * @description:
 */

return [
    'partner'   => '2088xxx',
    'md5_key'   => 'xxxxxxxxxx',
    'rsa_private_key'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rsa_private_key.pem',
    "notify_url"	=> 'http://test.helei.com/pay-notify.html',
    "return_url"	=> 'http://test.helei.com/return-url.html',
    "time_expire"	=> '14',
];