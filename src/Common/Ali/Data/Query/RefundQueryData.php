<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 下午1:45
 */

namespace Payment\Common\Ali\Data\Query;


use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
 * 支付宝退款查询
 * Class RefundQueryData
 *
 * @property string $trade_no 支付宝的订单号，优先使用
 * @property string $out_trade_no 商户系统内部的订单号
 * @property string $refund_no  请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
 *
 * @package Payment\Common\Ali\Data\Query
 */
class RefundQueryData extends QueryBaseData
{

    protected function getBizContent()
    {
        $content = [
            'out_trade_no'    => $this->out_trade_no,
            'trade_no'        => $this->trade_no,
            'out_request_no'    => $this->refund_no,
        ];

        $content = ArrayUtil::paraFilter($content);// 过滤掉空值，下面不用在检查是否为空
        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    protected function checkDataParam()
    {
        $tradeNo = $this->trade_no;// 支付宝交易号，查询效率高
        $outTradeNo = $this->out_trade_no;// 商户订单号，查询效率低，不建议使用

        // 二者不能同时为空
        if (empty($outTradeNo) && empty($tradeNo)) {
            throw new PayException('必须提供支付宝交易号或者商户网站唯一订单号。建议使用支付宝交易号');
        }

        $refundNo = $this->refund_no;
        if (empty($refundNo)) {
            throw new PayException('支付宝查询退款，必须传入提款的请求号。如果在退款请求时未传入，则该值为创建交易时的外部交易号');
        }
    }
}