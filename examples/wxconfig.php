<?php
/**
 * @author: helei
 * @createTime: 2016-08-01 11:37
 * @description: 微信配置文件
 */

return [
    'use_sandbox'       => true,// 是否使用 微信支付仿真测试系统

    'app_id'            => 'wxxxxxx',  // 公众账号ID
    'mch_id'            => 'xxxxx',// 商户id
    'md5_key'           => 'xxxxxxx',// md5 秘钥
    'app_cert_pem'      => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR .  'pem' . DIRECTORY_SEPARATOR . 'weixin_app_cert.pem',
    'app_key_pem'       => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR .  'pem' . DIRECTORY_SEPARATOR . 'weixin_app_key.pem',
    'sign_type'         => 'MD5',// MD5  HMAC-SHA256
    'limit_pay'         => [
        //'no_credit',
    ],// 指定不能使用信用卡支付   不传入，则均可使用
    'fee_type'          => 'CNY',// 货币类型  当前仅支持该字段

    'notify_url'        => 'https://helei112g.github.io/v1/notify/wx',

    'redirect_url'      => 'https://helei112g.github.io/',// 如果是h5支付，可以设置该值，返回到指定页面

    'return_raw'        => false,// 在处理回调时，是否直接返回原始数据，默认为true
];
