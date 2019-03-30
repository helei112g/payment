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

use Payment\Client\Refund;
use Payment\Common\PayException;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$refundNo = time() . rand(1000, 9999);
$data     = [
    'out_trade_no' => '9354737499',
    'date'         => '20170430',
    'refund_no'    => $refundNo,
    'refund_fee'   => 0.01,
    'reason'       => '测试帐号退款',
    'operator_id'  => '9999',
];

try {
    $ret = Refund::run(Config::CMB_REFUND, $cmbConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
