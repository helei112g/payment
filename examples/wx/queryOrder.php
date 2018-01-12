<?php
/**
 * 查询支付的订单
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午3:43
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');

$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$data = [
    'out_trade_no' => '14935505602169',
    'transaction_id' => '20170430190922203640695',
];

try {
    $ret = Query::run(Config::WX_CHARGE, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
