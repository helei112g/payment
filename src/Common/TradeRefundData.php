<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 17:03
 * @description:
 */

namespace Payment\Common;



use Payment\Contracts\DataStruct;

class TradeRefundData extends DataStruct
{
    // 第三方的订单号
    public $transaction_id;

    // 商户订单号
    public $order_no;

    // 商户退款单号
    public $refund_no;

    // 该笔订单总金额
    public $amount;

    // 退款金额
    public $refund_fee;

    // 额外数据(退款理由)
    public $description;
}