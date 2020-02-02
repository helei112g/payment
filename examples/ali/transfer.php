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

$params = [
    'trans_no'        => time(),
    'payee_type'      => 'ALIPAY_LOGONID', // ALIPAY_USERID：支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成; ALIPAY_LOGONID：支付宝登录号，支持邮箱和手机号格式
    'payee_account'   => 'aaqlmq0729@sandbox.com', // ALIPAY_USERID: 2088102169940354      ALIPAY_LOGONID：aaqlmq0729@sandbox.com
    'amount'          => '1000',
    'remark'          => '转账拉，有钱了',
    'payer_show_name' => '一个未来的富豪',
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::ALIPAY, $aliConfig);
    $res    = $client->transfer($params);
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
