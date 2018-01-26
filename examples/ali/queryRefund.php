<?php
/**
 * 查询订单退款状态
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午5:55
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

$data = [
    'out_trade_no' => '15043296209218',
    'trade_no' => '2017090221001004350200242476',
    'refund_no' => '15043420895504',
];

try {
    $ret = Query::run(Config::ALI_REFUND, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
