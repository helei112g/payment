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


date_default_timezone_set('Asia/Shanghai');
$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$refundNo = time() . rand(1000, 9999);
$data     = [
    'trade_no'       => '15805490027722',
    'transaction_id' => '4988319909620200201172451651106',
    'total_fee'      => '5.52',
    'refund_fee'     => '5.52',
    'refund_no'      => $refundNo,
    'refund_account' => 'REFUND_SOURCE_REC', // REFUND_RECHARGE:可用余额退款  REFUND_UNSETTLED:未结算资金退款（默认）
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::WECHAT, $wxConfig);
    $res    = $client->refund($data);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\GatewayException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\ClassNotFoundException $e) {
    echo $e->getMessage();
    exit;
}

var_dump($res);
