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

$params = [
    'type' => 'trade', //trade：交易账单，refund：退款账单，默认是：trade

    // trade
    'date'        => time(),
    'message_key' => time(),

    // refund
    'start_time'  => strtotime('-2days'),
    'end_time'    => strtotime('-1days'),
    'operator_id' => '111',
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::CMB, $cmbConfig);
    $res    = $client->billDownload($params);
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
