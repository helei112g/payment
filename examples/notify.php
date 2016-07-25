<?php
/**
 * @author: helei
 * @createTime: 2016-07-25 15:57
 * @description: 支付通知回调
 */

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/testNotify.php';

use Payment\NotifyContext;
use Payment\Common\PayException;
use Payment\Config;

$aliconfig = require_once __DIR__ . '/aliconfig.php';

$notify = new NotifyContext();

$callback = new TestNotify();

try {
    // 支付宝回调
    $notify->initNotify(Config::ALI, $aliconfig);
    $notify->notify($callback);
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}