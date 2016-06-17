<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 13:35
 * @description:
 */

namespace Payment\Common;



use Payment\Contracts\DataStruct;

class TradeInfoData extends DataStruct
{
    // 商品标题
    public $subject;

    // 商品描述
    public $body;

    // 商品金额
    public $amount;

    // 支付渠道
    public $channel;

    // 商户唯一订单号
    public $order_no;

    // 购买者标识  支付宝对应 buyer_id字段，微信对应openid字段
    public $buyer_id;

    // 交易状态
    public $trade_state;

    // 第三方订单号
    public $transaction_id;

    // 交易完成时间
    public $time_end;

    // 额外数据
    public $description;
}