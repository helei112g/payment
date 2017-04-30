<?php
/**
 * 招商一网通支付
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 上午11:55
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Config;
use Payment\Client\Helper;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$channel = 'cmb_pub_key';

try {
    $ret = \Payment\Client\Helper::run(Config::CMB_PUB_KEY, $cmbConfig);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
