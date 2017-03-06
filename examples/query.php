<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:28
 * @description: 交易状态查询
 */

require_once __DIR__ . '/../autoload.php';

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

use Payment\QueryContext;
use Payment\Common\PayException;
use Payment\Client\Query;

$query = new QueryContext();

// ali: 14887239163319   14887240631516
// wx:  14887927481312    14887931921301
$queryValue = '14887239163319';
$queryName = Query::NAME_SELF_QUERY;

$type = 'ali_charge';
try {
    $ret = Query::run($type, $aliConfig, $queryName, $queryValue);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
