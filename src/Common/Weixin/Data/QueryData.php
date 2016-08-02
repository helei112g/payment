<?php
/**
 * @author: helei
 * @createTime: 2016-08-02 17:00
 * @description:
 */

namespace Payment\Common\Weixin\Data;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;


/**
 * Class QueryData
 *
 * @property string $transaction_id 支付宝交易号
 * @property string $order_no 商户网站唯一订单号
 * @property string $refund_no  商户退款单号
 * @property string $refund_id  微信退款单号
 *
 * @package Payment\Common\Weixin\Data
 * anthor helei
 */
class QueryData extends WxBaseData
{

    protected function buildData()
    {
        $this->retData = [
            'appid' => $this->appId,
            'mch_id'    => $this->mchId,
            'nonce_str' => $this->nonceStr,
            'transaction_id'    => $this->transaction_id,
            'out_trade_no'  => $this->order_no,
            'out_refund_no' => $this->refund_no,
            'refund_id' => $this->refund_id,
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    protected function checkDataParam()
    {
        $transaction_id = $this->transaction_id;// 支付宝交易号，查询效率高
        $order_no = $this->order_no;// 商户订单号，查询效率低，不建议使用

        // 二者不能同时为空
        if (empty($transaction_id) && empty($order_no)) {
            throw new PayException('必须提供微信交易号或者商户网站唯一订单号。建议使用微信交易号');
        }
    }
}