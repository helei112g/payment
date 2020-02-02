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
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

$refundNo = time() . rand(1000, 9999);

$refundData = [
    'trade_no'       => '15806490592761',
    'transaction_id' => '', // 支付宝交易号， 与 trade_no 必须二选一
    'refund_fee'     => '0.01',
    'reason'         => '我要退款',
    'refund_no'      => $refundNo,
];
var_dump($refundData);
// 使用
try {
    $client = new \Payment\Client(\Payment\Client::ALIPAY, $aliConfig);
    $res    = $client->refund($refundData);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\GatewayException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\ClassNotFoundException $e) {
    echo $e->getMessage();
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
var_dump($res);
