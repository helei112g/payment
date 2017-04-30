<?php
/**
 * 退款操作
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午5:43
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Refund;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

$refundNo = time() . rand(1000, 9999);
$data = [
    'out_trade_no' => '14935460661343',
    'refund_fee' => '0.01',
    'reason' => '测试帐号退款',
    'refund_no' => $refundNo,
];

try {
    $ret = Refund::run(Config::ALI_REFUND, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);