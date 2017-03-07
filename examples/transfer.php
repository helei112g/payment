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
$data = [
    'trans_no' => time(),
    'payee_type' => 'ALIPAY_LOGONID',
    'payee_account' => 'aaqlmq0729@sandbox.com',// ALIPAY_USERID: 2088102169940354      ALIPAY_LOGONID：aaqlmq0729@sandbox.com
    'amount' => '100',
    'remark' => '退还 14888693949249 订单的测试金额',
    'payer_show_name' => '何磊',
];

$channel = 'ali_transfer';//wx_transfer   ali_transfer
try {
    $ret = Transfer::run($channel, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
