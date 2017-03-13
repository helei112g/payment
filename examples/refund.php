<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 11:00
 * @description: 退款调试接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Refund;


$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

// ali: 123123123q    123123123w
// wx:  123123123q    123123123w

$refundNo = time() . rand(1000, 9999);
// ali退款
$data = [
    'out_trade_no' => '14893825198432',
    'refund_fee' => '0.01',
    'reason' => '测试帐号退款',
    'refund_no' => $refundNo,
];

// wx退款
/*$data = [
    'out_trade_no' => '14892259638926',
    'total_fee' => '0.01',
    'refund_fee' => 0.01,
    'refund_no' => $refundNo,
];*/
var_dump($refundNo);

$channel = 'ali_refund';//xx_refund

if (stripos($channel, 'ali') !== false) {
    $config = $aliConfig;
} else {
    $config = $wxConfig;
}

try {
    $ret = Refund::run($channel, $config, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);exit;
