<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:28
 * @description: 交易状态查询
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;

date_default_timezone_set('Asia/Shanghai');

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';
$cmbConfig = require_once __DIR__ . '/cmbconfig.php';

// 支付查询
//ali_charge 数组参数二选一即可
/*$data = [
    'out_trade_no' => '14925658629435',
    'trade_no' => '2017041921001004350200159074',
];*/
//wx_charge
/*$data = [
    'out_trade_no' => '14898526325316',
    'transaction_id' => '',
];*/
// cmb_charge
/*$data = [
    'out_trade_no' => '9336161758',
    'date' => '20170428',
    'transaction_id' => '17242823500000000010',
];*/

// 退款查询
// ali_refund
/*$data = [
    'out_trade_no' => '14925658629435',
    'trade_no' => '',
    'refund_no' => '14925687382755',
];*/
// wx_refund
/*$data = [
    'out_trade_no' => '123123123q',
    'refund_no' => '',
    'transaction_id' => '',
    'refund_id' => '',
];*/
// cmb_refund
$data = [
    'out_trade_no' => '9336161758',
    'refund_no' => '',// 商户退款流水号,长度不超过20位
    'date' => '20170428',
    'refund_id' => '',// 银行退款流水号,长度不超过20位
];

// 转账查询
// ali_transfer
/*$data = [
    'trans_no' => '1488897403',
    'transaction_id' => '20170307110070001502680000002892',
];*/
// wx_transfer
/*$data = [
    'trans_no' => '1489852933',
];*/


$type = 'cmb_refund';// xx_charge  xx_refund   xx_transfer

if (stripos($type, 'ali') !== false) {
    $config = $aliConfig;
} elseif (stripos($type, 'wx') !== false) {
    $config = $wxConfig;
} else {
    $config = $cmbConfig;
}

try {
    $ret = Query::run($type, $config, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
