<?php
/**
 * 扫码支付
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午4:29
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

// 订单信息
$orderNo = time() . rand(1000, 9999);
$payData = [
    'body'    => 'ali qr pay',
    'subject'    => '测试支付宝扫码支付',
    'order_no'    => $orderNo,
    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'amount'    => '0.01',// 单位为元 ,最小为0.01
    'return_param' => '123123',
    // 'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'goods_type' => '1',// 0—虚拟类商品，1—实物类商品
    'store_id' => '',

    'operator_id' => '',
    'terminal_id' => '',// 终端设备号(门店号或收银设备ID) 默认值 web
];


try {
    $url = Charge::run(Config::ALI_CHANNEL_QR, $aliConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo $url;
