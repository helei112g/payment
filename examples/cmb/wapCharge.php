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
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$tradeNo = time() . rand(1000, 9999);
$data    = [
    'date'        => time(),
    'trade_no'    => $tradeNo,
    'amount'      => 1.00,
    'time_expire' => time() + 1000,
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::CMB, $cmbConfig);
    $res    = $client->pay(\Payment\Client::CMB_CHANNEL_WAP, $data);
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
