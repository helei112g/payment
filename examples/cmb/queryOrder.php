<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午2:18
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Config;
use Payment\Client\Query;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$data = [
    'out_trade_no' => '9336161758',
    'date' => '20170428',
    'transaction_id' => '17242823500000000010',
];

try {
    $ret = Query::run(Config::CMB_CHARGE, $cmbConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);