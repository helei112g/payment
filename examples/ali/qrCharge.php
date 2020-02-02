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
    'body'         => 'ali qr pay',
    'subject'      => '测试支付宝扫码支付',
    'trade_no'     => $tradeNo,
    'time_expire'  => time() + 1000, // 表示必须 1000s 内付款
    'amount'       => '0.01', // 单位为元 ,最小为0.01
    'return_param' => '123123',
    // 'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'goods_type' => '1', // 0—虚拟类商品，1—实物类商品
    'store_id'   => '',

    'operator_id' => '',
    'terminal_id' => '', // 终端设备号(门店号或收银设备ID) 默认值 web
];


// 使用
try {
    $client = new \Payment\Client(\Payment\Client::ALIPAY, $aliConfig);
    $res    = $client->pay(\Payment\Client::ALI_CHANNEL_QR, $payData);
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

echo \Payment\Helpers\StrUtil::toQRImg($res['qr_code']);
