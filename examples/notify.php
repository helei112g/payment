<?php
/**
 * @author: helei
 * @createTime: 2016-07-25 15:57
 * @description: 支付通知回调
 */

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/testNotify.php';

use Payment\Common\PayException;
use Payment\Client\Notify;

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

$callback = new TestNotify();

$type = 'wx_charge';// xx_charge

if (stripos($type, 'ali') !== false) {
    $config = $aliConfig;
} else {
    $config = $wxConfig;
}

try {
    //$retData = Notify::getNotifyData($type, $config);// 获取第三方的原始数据

    $ret = Notify::run($type, $config, $callback);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
exit;
