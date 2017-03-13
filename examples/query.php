<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:28
 * @description: 交易状态查询
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Query;

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

// ali: 123123123q(14888971346355)    123123123w
// wx:  123123123q

// 支付查询
//ali_charge
$data = [
    'out_trade_no' => '14893825198432',
    //'trade_no' => '2017030721001004350200139475',
];
//wx_charge
/*$data = [
    'out_trade_no' => '123123123q',
    'transaction_id' => '',
];*/

// 退款查询
// ali_refund
/*$data = [
    'out_trade_no' => '123123123q',
    'trade_no' => '2017030721001004350200139475',
    'refund_no' => '14888971346355',
];*/
// wx_refund
/*$data = [
    'out_trade_no' => '123123123q',
    'refund_no' => '',
    'transaction_id' => '',
    'refund_id' => '',
];*/

// 转账查询
// ali_transfer
/*$data = [
    'trans_no' => '1488897403',
    'transaction_id' => '20170307110070001502680000002892',
];*/
// wx_transfer
/*$data = [
    'trans_no' => '123123123q',
];*/

$type = 'ali_charge';// xx_charge  xx_refund   xx_transfer

if (stripos($type, 'ali') !== false) {
    $config = $aliConfig;
} else {
    $config = $wxConfig;
}

try {
    $ret = Query::run($type, $config, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
