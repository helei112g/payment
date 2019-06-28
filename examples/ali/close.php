<?php
/**
 * 关闭订单操作
 * Created by PhpStorm.
 * User: biker
 * Date: 2019/06/28
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

$data = [
    'out_trade_no' => '15043296209218',
    'trade_no' => '',// 支付宝交易号， 与 out_trade_no 必须二选一
];

try {
    $ret = \Payment\Client\Close::run(Config::ALI_CLOSE, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
