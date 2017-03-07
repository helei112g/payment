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

// 微信的配置文件
$wxConfig = require_once __DIR__ . '/wxconfig.php';

// ali_transfer
/*$data = [
    'trans_no' => time(),
    'payee_type' => 'ALIPAY_LOGONID',
    'payee_account' => 'aaqlmq0729@sandbox.com',// ALIPAY_USERID: 2088102169940354      ALIPAY_LOGONID：aaqlmq0729@sandbox.com
    'amount' => '100',
    'remark' => '退还 14888693949249 订单的测试金额',
    'payer_show_name' => '何磊',
];*/

// wx_transfer
$data = [
    'trans_no' => time(),
    'openid' => 'xxxxxxx',
    'check_name' => 'OPTION_CHECK',// NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名   OPTION_CHECK：针对已实名认证的用户才校验真实姓名
    'payer_real_name' => 'hahah',
    'amount' => '0.01',
    'desc' => '退还 14887931921301 订单的测试金额',
    'spbill_create_ip' => '127.0.0.1',
];

$channel = 'wx_transfer';//wx_transfer   ali_transfer
try {
    $ret = Transfer::run($channel, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
