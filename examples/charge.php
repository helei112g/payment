<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 09:25
 * @description: 测试支付接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Charge;

// 微信支付 招商支付，自己的应用必须设置时区
date_default_timezone_set('Asia/Shanghai');

$orderNo = time() . rand(1000, 9999);
// 订单信息
$payData = [
    'body'    => 'test body',
    'subject'    => 'test subject',
    'order_no'    => $orderNo,
    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'amount'    => '3.01',// 单位为元 ,最小为0.01
    'return_param' => '123',
    'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址

    // 条码支付
    'operator_id' => '',
    'terminal_id' => '',// 终端设备号(门店号或收银设备ID) 默认值 web
    'alipay_store_id' => '',
    'scene' => 'bar_code',// 条码支付：bar_code 声波支付：wave_code
    'auth_code' => '1231212232323123123',
];

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';
$cmbConfig = require_once __DIR__ . '/cmbconfig.php';

// ali_app  ali_wap  ali_web  ali_qr  ali_bar
// wx_app    wx_pub   wx_qr   wx_bar  wx_lite   wx_wap
// cmb_app
$channel = 'cmb_app';

if (stripos($channel, 'ali') !== false) {
    // 支付宝公有
    $payData['goods_type'] = 1;
    $payData['store_id'] = '';

    // 支付宝电脑支付（即时到账）
    $payData['qr_mod'] = '';//0、1、2、3 几种方式 建议不填
    $payData['paymethod'] = 'creditPay';// creditPay  directPay

    $config = $aliConfig;
} elseif (stripos($channel, 'wx') !== false) {
    $payData['openid'] = 'o-e_mwTXTaxEhBM8xDoj1ui1f950';
    $payData['product_id'] = '123';

    $config = $wxConfig;
} else {
    $payData['date'] = date('Ymd');
    $payData['order_no'] = substr($payData['order_no'], 2, 10);// 10位数字，由商户生成，一天内不能重复。
    $payData['agr_no'] = '430802198004014358';// 建议用身份证
    $payData['serial_no'] = time() . rand(1000, 9999);// 协议开通请求流水号，开通协议时必填
    $payData['user_id'] = 888;
    $payData['mobile'] = '13500007107';
    $payData['lon'] = '';
    $payData['lat'] = '';
    $payData['risk_level'] = '3';

    $config = $cmbConfig;
}

try {
    $ret = Charge::run($channel, $config, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

if (stripos($channel, 'cmb') !== false) {
    return $ret;
} elseif (is_array($ret)) {
    var_dump($ret);
} else {
    //header('Location:' . $ret);
    echo htmlspecialchars($ret);
}
exit;
