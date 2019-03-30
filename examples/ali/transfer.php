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

use Payment\Client\Transfer;
use Payment\Common\PayException;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

$data = [
    'trans_no'        => time(),
    'payee_type'      => 'ALIPAY_LOGONID',
    'payee_account'   => 'aaqlmq0729@sandbox.com', // ALIPAY_USERID: 2088102169940354      ALIPAY_LOGONID：aaqlmq0729@sandbox.com
    'amount'          => '1000',
    'remark'          => '转账拉，有钱了',
    'payer_show_name' => '一个未来的富豪',
];

try {
    $ret = Transfer::run(Config::ALI_TRANSFER, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
