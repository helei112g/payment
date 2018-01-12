<?php
/**
 * 刷卡支付
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午3:12
 */

require_once __DIR__ . '/../../vendor/autoload.php';

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
    'amount'    => '0.01',// 微信沙箱模式，需要金额固定为0.01
    'return_param' => '123',
    'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'terminal_id' => 'web',// 终端设备号(门店号或收银设备ID) 默认值 web
    'auth_code' => '1231212232323123123',

    // 如果是服务商，请提供以下参数
    'sub_appid' => '',//微信分配的子商户公众账号ID
    'sub_mch_id' => '',// 微信支付分配的子商户号
];

try {
    $ret = Charge::run(Config::WX_CHANNEL_BAR, $wxConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
