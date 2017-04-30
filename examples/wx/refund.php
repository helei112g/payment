<?php
/**
 * 退款处理  金额必须是 3.01
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午3:51
 */


require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Refund;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$refundNo = time() . rand(1000, 9999);
$data = [
    'out_trade_no' => '14935385689468',
    'total_fee' => '3.01',
    'refund_fee' => '3.01',
    'refund_no' => $refundNo,
];

try {
    $ret = Refund::run(Config::WX_REFUND, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);