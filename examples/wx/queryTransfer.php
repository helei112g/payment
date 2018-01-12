<?php
/**
 * 转账查询 没有沙箱模式
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午3:48
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');

$wxConfig = require_once __DIR__ . '/../wxconfig.php';

$data = [
    'trans_no' => '1489852933',
];

try {
    $ret = Query::run(Config::WX_CHARGE, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
