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
    'trade_no' => '15805490027722',
    //'refund_no'      => '15805498596435',
    //'transaction_id' => '4988319909620200201172451651106',
    //'refund_id'      => '4196711984120200201173913842',
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
