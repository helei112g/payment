<?php
/**
 * @author: helei
 * @createTime: 2016-08-01 11:37
 * @description: 微信配置文件
 */

return [
    'app_id'    => 'wxxxxx',  // 公众账号ID
    'mch_id'    => 'xxxxx',// 商户id
    'md5_key'   => 'xxxxxx',// md5 秘钥

    'notify_url'    => 'https://helei112g.github.io/',
    'time_expire'	=> '14',

    // 涉及资金流动时 退款  转款，需要提供该文件
    'cert_path' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem',
    'key_path'  => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR . 'apiclient_key.pem',
];