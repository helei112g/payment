<?php
/**
 * Created by PhpStorm.
 * User: biker
 * Date: 2018/06/28
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$data = [
    'out_trade_no' => '14935385689468',
];

try {
    $ret = \Payment\Client\Close::run(Config::WX_CLOSE, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
