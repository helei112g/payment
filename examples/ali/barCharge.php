<?php
/**
 * 条码支付
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午4:32
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

// 订单信息
$orderNo = time() . rand(1000, 9999);
$payData = [
    'body'    => 'ali bar pay',
    'subject'    => '测试支付宝条码支付',
    'order_no'    => $orderNo,
    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'amount'    => '0.01',// 单位为元 ,最小为0.01
    'return_param' => '123123',
    'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'goods_type' => '1',
    'store_id' => '',
    'operator_id' => '',
    'terminal_id' => '',// 终端设备号(门店号或收银设备ID) 默认值 web
    'alipay_store_id' => '',
    'scene' => 'bar_code',// 条码支付：bar_code 声波支付：wave_code
    'auth_code' => '1231212232323123123',
];

try {
    $ret = Charge::run(Config::ALI_CHANNEL_BAR, $aliConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);