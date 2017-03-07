<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 09:25
 * @description: 测试支付接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Config;
use Payment\Common\PayException;
use Payment\Client\Charge;

date_default_timezone_set('Asia/Shanghai');


$orderNo = time() . rand(1000, 9999);
// 订单信息
$payData = [
    'body'    => 'test body',
    'subject'    => 'test subject',
    'order_no'    => '14888693949249',
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
    'auth_code' => '',

    // web支付
    'qr_mod' => '',//0、1、2、3 几种方式
    'paymethod' => 'creditPay',// creditPay  directPay

    'client_ip' => '127.0.0.1',

    'openid' => 'ottkxxxxxxx',
    'product_id' => '123',
];

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

// ali_app  ali_wap  ali_direct  ali_qr  ali_bar
// wx_app    wx_pub   wx_qr   wx_bar  wx_lite   wx_wap
$channel = 'ali_wap';
try {
    $ret = Charge::run($channel, $aliConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

if ($channel === Config::ALI_CHANNEL_APP) {
    echo htmlspecialchars($ret);
    exit;
} elseif ($channel === Config::ALI_CHANNEL_QR) {
    $url = \Payment\Utils\DataParser::toQRimg($ret);// 内部会用到google 生成二维码的api  可能有些同学反应很慢
    echo "<img alt='支付宝扫码支付' src='{$url}' style='width:150px;height:150px;'/>";
    exit;
} elseif ($channel === Config::ALI_CHANNEL_BAR) {// 条码支付，直接返回支付结果
    var_dump($ret);
    exit;
} elseif ($channel === Config::WX_CHANNEL_QR) {// 二维码生成推荐使用：endroid/qrcode
    $url = \Payment\Utils\DataParser::toQRimg($ret);
    echo "<img alt='微信扫码支付' src='{$url}' style='width:150px;height:150px;'/>";
    return $ret;
} elseif ($channel === Config::WX_CHANNEL_PUB) {
    $json = $ret;
    var_dump($json);
} elseif (stripos($channel, 'wx') !== false) {
    var_dump($ret);
    exit;
} elseif (stripos($channel, 'ali') !== false) {
    // 跳转支付宝
    header("Location:{$ret}");
} else {
    var_dump($ret);
    exit;
}
