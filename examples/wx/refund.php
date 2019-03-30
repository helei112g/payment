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
use Payment\Common\WxConfig;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$refundNo = time() . rand(1000, 9999);
$data     = [
    'out_trade_no'   => '14935385689468',
    'total_fee'      => '3.01',
    'refund_fee'     => '3.01',
    'refund_no'      => $refundNo,
    'refund_account' => WxConfig::REFUND_RECHARGE, // REFUND_RECHARGE:可用余额退款  REFUND_UNSETTLED:未结算资金退款（默认）
];

try {
    $ret = Refund::run(Config::WX_REFUND, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
