<?php
/**
 * 转账查询
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午6:00
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

$data = [
    'trans_no' => '1493546355',
    'transaction_id' => '20170430110070001500680000004577',
];

try {
    $ret = Query::run(Config::ALI_TRANSFER, $aliConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);