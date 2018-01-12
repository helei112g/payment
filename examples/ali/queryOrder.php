<?php
/**
 * 查询订单
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午5:35
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

// 二者不能同时为空
$data = [
    'out_trade_no' => '15043337047336',
    //'trade_no' => '2017090221001004350200242476',
];

try {
    $ret = Query::run(Config::ALI_CHARGE, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
