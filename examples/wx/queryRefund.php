<?php
/**
 * 查询退款订单
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午3:43
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');

$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$data = [
    'out_trade_no' => '14935385689468',
    'refund_no' => '14935506214648',
    'transaction_id' => '12345678920170430191024123337865',
    'refund_id' => '1234567892017043019102412333',
];

try {
    $ret = Query::run(Config::WX_REFUND, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);