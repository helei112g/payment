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
    'channel' => \Payment\Client::TRANSFER_CHANNEL_ACCOUNT, // account: 转微信，bank：转银行

    // account的参数
    'device_info'     => '',
    'trans_no'        => time(),
    'openid'          => 'o-e_mwTXTaxEhBM8xDoj1ui1f950',
    'check_name'      => 'NO_CHECK', // NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名
    'payer_real_name' => 'dayu',
    'amount'          => '1',
    'desc'            => '测试转账',
    'client_ip'       => '127.0.0.1',

    // bank 的参数
    /*'trans_no' => time(),
    'enc_bank_no' => '',
    'enc_true_name' => '',
    'bank_code' => '',
    'amount'           => '1',
    'desc'             => '测试转账',*/

];

try {
    $client = new \Payment\Client(\Payment\Client::WECHAT, $wxConfig);
    $client->transfer($data);
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
