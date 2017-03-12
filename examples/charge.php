<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 09:25
 * @description: 测试支付接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Charge;

date_default_timezone_set('Asia/Shanghai');

$orderNo = time() . rand(1000, 9999);
// 订单信息
$payData = [
    'body'    => 'test body',
    'subject'    => 'test subject',
    'order_no'    => $orderNo,
    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'amount'    => '0.01',// 单位为元 ,最小为0.01
    'return_param' => '123',

    // 支付宝公有
    'goods_type' => 1,
    'store_id' => '',

    // 条码支付
    'operator_id' => '',
    'terminal_id' => '',// 终端设备号(门店号或收银设备ID) 默认值 web
    'alipay_store_id' => '',
    'scene' => 'bar_code',// 条码支付：bar_code 声波支付：wave_code
    'auth_code' => '1231212232323123123',

    // web支付
    'qr_mod' => '',//0、1、2、3 几种方式
    'paymethod' => 'creditPay',// creditPay  directPay

    'client_ip' => '127.0.0.1',

    'openid' => 'ohQeiwnNrAg5bD7EVvmGFIhba--k',
    'product_id' => '123',
];

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

// ali_app  ali_wap  ali_web  ali_qr  ali_bar
// wx_app    wx_pub   wx_qr   wx_bar  wx_lite   wx_wap
$channel = 'ali_wap';

if (stripos($channel, 'ali') !== false) {
    $config = $aliConfig;
} else {
    $config = $wxConfig;
}

try {
    $ret = Charge::run($channel, $config, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

if (is_array($ret)) {
    var_dump($ret);
} else {
    echo htmlspecialchars($ret);
}
exit;
