<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Client\Query;
use Payment\Common\PayException;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$data = [
    'out_trade_no' => '9354737499',
    'refund_no'    => '', // 商户退款流水号,长度不超过20位
    'date'         => '20170430',
    'refund_id'    => '', // 银行退款流水号,长度不超过20位
];

try {
    $ret = Query::run(Config::CMB_REFUND, $cmbConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
