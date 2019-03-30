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

$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$data = [
    'out_trade_no'   => '14935385689468',
    'refund_no'      => '14935506214648',
    'transaction_id' => '12345678920170430191024123337865',
    'refund_id'      => '1234567892017043019102412333',
];

try {
    $ret = Query::run(Config::WX_REFUND, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
