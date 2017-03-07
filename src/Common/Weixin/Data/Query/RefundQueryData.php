<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 下午1:32
 */

namespace Payment\Common\Weixin\Data\Query;


use Payment\Common\PayException;
use Payment\Common\Weixin\Data\WxBaseData;
use Payment\Utils\ArrayUtil;

/**
 * 微信退款接口查询
 *
 * @property string $transaction_id 微信的订单号，优先使用
 * @property string $out_trade_no 商户系统内部的订单号
 *
 * Class RefundQueryData
 * @package Payment\Common\Weixin\Data\Query
 */
class RefundQueryData extends WxBaseData
{

    protected function buildData()
    {
        $this->retData = [
            'appid' => $this->appId,
            'mch_id'    => $this->mchId,
            'device_info' => $this->terminal_id,
            'nonce_str' => $this->nonceStr,
            'sign_type' => $this->signType,

            'transaction_id'    => $this->transaction_id,
            'out_trade_no'  => $this->out_trade_no,
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    protected function checkDataParam()
    {
        $transaction_id = $this->transaction_id;// 微信交易号，查询效率高
        $order_no = $this->out_trade_no;// 商户订单号，查询效率低，不建议使用

        // 二者不能同时为空
        if (empty($transaction_id) && empty($order_no)) {
            throw new PayException('查询退款  必须提供微信交易号或商户网站唯一订单号。建议使用微信交易号');
        }
    }
}