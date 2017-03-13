<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 15:46
 * @description: 批量转款测试
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Transfer;

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

// ali_transfer
$data = [
    'trans_no' => time(),
    'payee_type' => 'ALIPAY_LOGONID',
    'payee_account' => 'aaqlmq0729@sandbox.com',// ALIPAY_USERID: 2088102169940354      ALIPAY_LOGONID：aaqlmq0729@sandbox.com
    'amount' => '0.01',
    'remark' => '转账拉，有钱了',
    'payer_show_name' => '何磊',
];

// wx_transfer
/*$data = [
    'trans_no' => time(),
    'openid' => '------',
    'check_name' => 'OPTION_CHECK',// NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名   OPTION_CHECK：针对已实名认证的用户才校验真实姓名
    'payer_real_name' => '何磊',
    'amount' => '0.01',
    'desc' => '测试转账',
    'spbill_create_ip' => '127.0.0.1',
];*/

$channel = 'ali_transfer';//wx_transfer   ali_transfer

if (stripos($channel, 'ali') !== false) {
    $config = $aliConfig;
} else {
    $config = $wxConfig;
}

try {
    $ret = Transfer::run($channel, $config, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
