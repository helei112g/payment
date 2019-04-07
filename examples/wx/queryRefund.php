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

$data = [
    'out_trade_no'   => '14935385689468',
    'refund_no'      => '14935506214648',
    'transaction_id' => '12345678920170430191024123337865',
    'refund_id'      => '1234567892017043019102412333',
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::WECHAT, $wxConfig);
    $res    = $client->refundQuery($data);
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
