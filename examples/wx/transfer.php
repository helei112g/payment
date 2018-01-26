<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午4:13
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Client\Transfer;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$data = [
    'trans_no' => time(),
    'openid' => 'o-e_mwTXTaxEhBM8xDoj1ui1f950',
    'check_name' => 'NO_CHECK',// NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名   OPTION_CHECK：针对已实名认证的用户才校验真实姓名
    'payer_real_name' => '何磊',
    'amount' => '1',
    'desc' => '测试转账',
    'spbill_create_ip' => '127.0.0.1',
];

try {
    $ret = Transfer::run(Config::WX_TRANSFER, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
