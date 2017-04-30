<?php
/**
 * 第三方支付回调处理
 * @author: helei
 * @createTime: 2016-07-25 15:57
 * @description: 支付通知回调
 */

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/testNotify.php';

use Payment\Common\PayException;
use Payment\Client\Notify;

date_default_timezone_set('Asia/Shanghai');

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';
$cmbConfig = require_once __DIR__ . '/cmbconfig.php';

$callback = new TestNotify();

$type = 'cmb_charge';// xx_charge

if (stripos($type, 'ali') !== false) {
    $config = $aliConfig;
} elseif (stripos($type, 'wx') !== false) {
    $config = $wxConfig;
} else {
    $config = $cmbConfig;
}

try {
    //$retData = Notify::getNotifyData($type, $config);// 获取第三方的原始数据，未进行签名检查

    $ret = Notify::run($type, $config, $callback);// 处理回调，内部进行了签名检查
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
exit;
