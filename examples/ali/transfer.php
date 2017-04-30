<?php
/**
 * 支付转账操作
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午5:57
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Transfer;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

$data = [
    'trans_no' => time(),
    'payee_type' => 'ALIPAY_LOGONID',
    'payee_account' => 'aaqlmq0729@sandbox.com',// ALIPAY_USERID: 2088102169940354      ALIPAY_LOGONID：aaqlmq0729@sandbox.com
    'amount' => '0.01',
    'remark' => '转账拉，有钱了',
    'payer_show_name' => '何磊',
];

try {
    $ret = Transfer::run(Config::ALI_TRANSFER, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);