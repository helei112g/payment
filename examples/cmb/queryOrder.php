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
    'out_trade_no'   => '9336161758',
    'date'           => '20170428',
    'transaction_id' => '17242823500000000010',
];

try {
    $ret = Query::run(Config::CMB_CHARGE, $cmbConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
