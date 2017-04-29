<?php

use Payment\Client\Helper;
use Payment\Common\PayException;

/**
 * 获取招商的公钥
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/29
 * Time: 上午9:57
 */

require_once __DIR__ . '/../autoload.php';

// 招商支付，自己的应用必须设置时区
date_default_timezone_set('Asia/Shanghai');

$cmbConfig = require_once __DIR__ . '/cmbconfig.php';

$channel = 'cmb_pub_key';

try {
    $ret = Helper::run($channel, $cmbConfig, []);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);