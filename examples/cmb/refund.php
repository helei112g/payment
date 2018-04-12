<?php
/**
 * 招商退款操作
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午6:03
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Config;
use Payment\Client\Refund;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$refundNo = time() . rand(1000, 9999);
$data = [
    'out_trade_no' => '9354737499',
    'date' => '20170430',
    'refund_no' => $refundNo,
    'refund_fee' => 0.01,
    'reason' => '测试帐号退款',
    'operator_id' => '9999',
];

try {
    $ret = Refund::run(Config::CMB_REFUND, $cmbConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
