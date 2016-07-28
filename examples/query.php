<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:28
 * @description: 交易状态查询
 */

require_once __DIR__ . '/../autoload.php';

$aliconfig = require_once __DIR__ . '/aliconfig.php';

use Payment\QueryContext;
use Payment\Common\PayException;
use Payment\Config;

$query = new QueryContext();

// 通过支付宝交易号查询，  推荐
$data = [
    'transaction_id'    => '2016011421001004330041239366',
];

// 通过订单号查询
/*$data = [
    'order_no'    => '2016011402433464',
];*/

try {
    $query->initQuery(Config::ALI, $aliconfig);
    $ret = $query->query($data);

} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

var_dump($ret);