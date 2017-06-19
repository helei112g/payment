<?php
/**
 * 微信wap支付
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午3:40
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');

$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$orderNo = time() . rand(1000, 9999);
// 订单信息
$payData = [
    'body'    => 'test body',
    'subject'    => 'test subject',
    'order_no'    => $orderNo,
    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'amount'    => '3.01',// 微信沙箱模式，需要金额固定为3.01
    'return_param' => '123',
    'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址

    //{"h5_info": {"type":"Wap","wap_url": "https://pay.qq.com","wap_name": "腾讯充值"}}
    'scene_info' => [
        'type' => 'Wap',// IOS  Android  Wap  腾讯建议 IOS  ANDROID 采用app支付
        'wap_url' => 'helei112g.github.io',//自己的 wap 地址
        'wap_name' => '测试充值',
    ],
];

try {
    $url = Charge::run(Config::WX_CHANNEL_WAP, $wxConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo $url;