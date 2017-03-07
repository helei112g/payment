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
use Payment\Config;
use Payment\Client\Query;

$query = new QueryContext();

// ali: 14887239163319(已退款)   14887240631516(已退款)    14888693949249
// wx:  14887927481312(已退款)   14887931921301

// 支付查询
//ali_charge
/*$data = [
    'out_trade_no' => '14888693949249',
    'trade_no' => '',
];*/
//wx_charge
/*$data = [
    'out_trade_no' => '14887931921301',
    'transaction_id' => '',
];*/

// 退款查询
// ali_refund
/*$data = [
    //'out_trade_no' => '14887240631516',
    'trade_no' => '2017030521001004330274482163',
    'refund_no' => '14888682939597',
];*/
// wx_refund
/*$data = [
    'out_trade_no' => '',
    'refund_no' => '',
    'transaction_id' => '',
    'refund_id' => '2007572001201703070896576492',
];*/

// 转账查询
// ali_transfer
/*$data = [
    //'trans_no' => '1488872748',// 1488872842    使用id转账
    'transaction_id' => '20170307110070001502680000001002',//  20170307110070001502680000001001  使用帐号转账
];*/
// wx_transfer
$data = [
    'trans_no' => '1488872748',
];

$type = 'wx_transfer';// xx_charge  xx_refund   xx_transfer
try {
    $ret = Query::run($type, $wxConfig, $data);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
