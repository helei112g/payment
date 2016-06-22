<?php
/**
 * @author: helei
 * @createTime: 2016-06-17 10:23
 * @description: 支付宝与weiixn的订单查询
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Common\ChargeChannel;
use Payment\Factory\TradeFactory;
use Payment\Common\PayException;


// 支付宝的回调
$payway = ChargeChannel::CHANNEL_IS_ALIPAY;

// 微信的回调
//$payway = ChargeChannel::CHANNEL_IS_WX;

$value = '2016061821001004170239716390';// 第三方交易号
$key = 'trade_no';// 可取值：out_trade_no：商户网站唯一订单号   trade_no： 第三方交易号

$api = TradeFactory::getInstance($payway);

try {
    $data = $api->tradeQuery($value, $key);

    /**
     * 'subject' => '美团美食购买'// 商品标题
     * 'body' => '购买蓉和小厨美食'// 商品描述
     * 'amount' => '133400'// 支付的总金额，单位为分
     * 'channel' => 'ali'// 支付通道 .此处可能值仅为： ali  wx
     * 'order_no' => '2016060504005139'// 商户唯一订单号
     * 'buyer_id' => '2088122159801601'// 购买者识别码。支付宝为：购买者邮箱，或者手机号码。weiixn为唯一识别码
     * 'trade_state' => 'SUCCESS'// 交易状态。SUCCESS—支付成功     REFUND—转入退款    NOTPAY—未支付
     * 'transaction_id' => '2016060521001004600254528027'// 第三方的流水号
     * 'time_end' => 2016-06-05 16:01:00'// 交易完成时间
     */
    var_dump($data);
} catch (PayException $e) {
    echo $e->errorMessage();
}