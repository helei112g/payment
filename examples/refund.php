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

// 支付宝配置文件
$aliconfig = require_once __DIR__ . '/aliconfig.php';

// 微信配置文件
$wxconfig = require_once __DIR__ . '/wxconfig.php';

// 退款数据
$reundData = [
    'refund_no' => createPayid(),
    'refund_data'   => [
        ['transaction_id' => '2016123121001004350200119946', 'amount'   => '10', 'refund_fee' => '1', 'reason' => '新版支付宝测试金额退款'],
        //['transaction_id' => '2016031521001004330271745693', 'amount'   => '0.01', 'refund_fee' => '0.01', 'reason' => '测试退款2'],
    ],
];
echo $reundData['refund_no'];
$refund = new RefundContext();
try {
    // 支付宝退款  备注：新版本支付宝退款，不支持批量，就算传入多个值，也只退一笔
    $type = Config::ALI;
    $refund->initRefund($type, $aliconfig);

    // 微信退款
    //$type = Config::WEIXIN;
    //$refund->initRefund(Config::WEIXIN, $wxconfig);

    $ret = $refund->refund($reundData);
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

if ($type == Config::WEIXIN || $aliconfig['ali_version']) {
    var_dump($ret);exit;
} else {
    // 跳转支付宝
    header("Location:{$ret}");
}
