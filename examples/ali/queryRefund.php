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

$data = [
    'trade_no'       => '15806490592761',
    'transaction_id' => '2020020222001440351000252550',
    'refund_no'      => '15806491408385',
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::ALIPAY, $aliConfig);
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
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
var_dump($res);
