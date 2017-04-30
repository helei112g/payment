<?php

use Payment\Client\Helper;
use Payment\Common\PayException;
/**
 * 辅助类的相关操作
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 下午12:04
 */

require_once __DIR__ . '/../autoload.php';

// 招商支付，自己的应用必须设置时区
date_default_timezone_set('Asia/Shanghai');

$cmbConfig = require_once __DIR__ . '/cmbconfig.php';

$payData = [
    'date' => date('Ymd'),
    'agr_no' => '430802198004014374',
    'serial_no' => time() . rand(1000, 9999),
    'mobile' => '13500007108',
    'user_id' => '100',
    'lon' => '',
    'lat' => '',
    'riskLevel' => '1',
];

// cmb_bind
$channel = 'cmb_bind';

try {
    $ret = Helper::run($channel, $cmbConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

if (stripos($channel, 'cmb') !== false) {
    return $ret;
} elseif (is_array($ret)) {
    var_dump($ret);
} else {
    //header('Location:' . $ret);
    echo htmlspecialchars($ret);
}