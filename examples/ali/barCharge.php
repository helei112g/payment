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

// 交易信息
$tradeNo = time() . rand(1000, 9999);
$payData = [
    'body'         => 'ali bar pay',
    'subject'      => '测试支付宝条码支付',
    'trade_no'     => $tradeNo,
    'time_expire'  => time() + 600, // 表示必须 600s 内付款
    'amount'       => '0.01', // 单位为元 ,最小为0.01
    'return_param' => '123123',
    // 'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'goods_type' => '1', // 0—虚拟类商品，1—实物类商品
    'store_id'   => '',

    'operator_id' => '',
    'terminal_id' => '', // 终端设备号(门店号或收银设备ID) 默认值 web
    'scene'       => 'bar_code', // 条码支付：bar_code 声波支付：wave_code
    'auth_code'   => '289580917433958393',
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::ALIPAY, $aliConfig);
    $res    = $client->pay(\Payment\Client::ALI_CHANNEL_BAR, $payData);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\GatewayException $e) {
    echo $e->getMessage();
    var_dump($e->getRaw());
    exit;
} catch (\Payment\Exceptions\ClassNotFoundException $e) {
    echo $e->getMessage();
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

var_dump($res);
