<?php
/**
 * @author: helei
 * @createTime: 2016-06-22 10:00
 * @description: 退款接口示例
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Factory\TradeFactory;
use Payment\Common\ChargeChannel;
use Payment\Common\PayException;
use Payment\Utils\Curl;

// 需要的数据
/**
 * 第三方的订单号
 * $transaction_id;
 *
 * 商户订单号
 * $order_no;
 *
 * 商户退款单号   不可重复。
 * 成规则：退款日期（8位）+流水号（3～24位）。不可重复，且退款日期必须是当天日期。
 * 流水号可以接受数字或英文字符，建议使用数字，但不可接受“000”。
 * eg: 201101120001
 * $refund_no;
 *
 * 该笔订单总金额
 * $amount;
 *
 * 退款金额
 * $refund_fee;
 *
 * 额外数据(退款理由)
 * $description;
 *
 * 退款成功后回调地址
 * $success_url
 */

$data = [
    'transaction_id'    => '2016061821001004170239716390',
    'order_no'  => 'F618376986170189',
    'refund_no' => date('Ymd', time()) . mt_rand(1000, 9999),
    'amount'    => 0.01,
    'refund_fee'    => 0.01,
    'description'   => '测试使用的金额',
    'success_url'   => 'http://www.tys.tiyushe.net/rt/TestRefund/notify.html'
];

// 支付宝的退款
$api = TradeFactory::getInstance(ChargeChannel::CHANNEL_IS_ALIPAY);

try {
    $url = $api->refund($data);
    
    header("Location:{$url}");
} catch (PayException $e) {
    echo $e->errorMessage();
}