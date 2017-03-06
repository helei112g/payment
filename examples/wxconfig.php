<?php
/**
 * @author: helei
 * @createTime: 2016-08-01 11:37
 * @description: 微信配置文件
 */

return [
    'app_id'        => 'wx5ec5e41b40d4a4dc',  // 公众账号ID
    'mch_id'        => '1372726302',// 商户id
    'md5_key'       => 'bEHBmGqsrT9kozTJt2pKxwMcSWOvKaSi',// md5 秘钥
    'cert_path'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR . 'weixin_app_cert.pem',
    'key_path'      => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR . 'weixin_app_key.pem',
    'sign_type'     => 'MD5',// MD5  HMAC-SHA256
    'limit_pay'     => [
        'no_credit',
    ],// 指定不能使用信用卡支付   不传入，则均可使用
    'fee_type' => 'CNY',// 货币类型  当前仅支持该字段

    'notify_url'    => 'https://helei112g.github.io/',

    'redirect_url' => 'https://helei112g.github.io/',// 如果是h5支付，可以设置该值，返回到指定页面

    'return_raw'                => false,// 在处理回调时，是否直接返回原始数据，默认为false
];
// ohQeiwnNrAg5bD7EVvmGFIhba--k


/*return [
    'app_id'        => 'wx75d7ac8440564066',  // 公众账号ID
    'mch_id'        => '1268738401',// 商户id
    'md5_key'       => '09E31D1AB4DE57A06DAC4834A7CB1432',// md5 秘钥
    'sign_type'     => 'MD5',// MD5  HMAC-SHA256
    'limit_pay'     => [
        'no_credit',
    ],// 指定不能使用信用卡支付   不传入，则均可使用
    'fee_type' => 'CNY',// 货币类型  当前仅支持该字段

    'notify_url'    => 'https://helei112g.github.io/',

    'return_raw'                => false,// 在处理回调时，是否直接返回原始数据，默认为false
];*/
// ottkCuO1PW1Dnh6PWFffNk-2MPbY