<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午2:29
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Config;
use Payment\Client\Query;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$data = [
    'out_trade_no' => '9354737499',
    'refund_no' => '',// 商户退款流水号,长度不超过20位
    'date' => '20170430',
    'refund_id' => '',// 银行退款流水号,长度不超过20位
];

try {
    $ret = Query::run(Config::CMB_REFUND, $cmbConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
