<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 11:00
 * @description: 退款调试接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\RefundContext;
use Payment\Common\PayException;
use Payment\Config;

//  生成退款单号 便于测试
function createPayid()
{
    return date('Ymdhis', time()).substr(floor(microtime()*1000),0,1).rand(0,9);
}

$aliconfig = require_once __DIR__ . '/aliconfig.php';

// 退款数据
$reundData = [
    'refund_no' => createPayid(),
    'refund_data'   => [
        ['transaction_id' => '2016011421001004330041239366', 'refund_fee' => '0.01', 'reason' => '测试退款1'],
        ['transaction_id' => '2016031521001004330271745693', 'refund_fee' => '0.01', 'reason' => '测试退款2'],
    ],
];

$refund = new RefundContext();
try {
    $refund->initRefund(Config::ALI, $aliconfig);
    $ret = $refund->refund($reundData);
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

// 跳转支付宝
header("Location:{$ret}");