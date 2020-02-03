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

$refundNo = time() . rand(1000, 9999);
$data     = [
    'trade_no'    => '9354737499',
    'date'        => time(),
    'refund_no'   => $refundNo,
    'refund_fee'  => 0.01,
    'reason'      => '测试帐号退款',
    'operator_id' => '9999',
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::CMB, $cmbConfig);
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
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
