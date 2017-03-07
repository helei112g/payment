<?php
/**
 * @author: helei
 * @createTime: 2016-07-26 18:18
 * @description:
 */

namespace Payment\Common\Ali\Data;

use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 * Class RefundData
 *
 * @property string $trade_no 支付宝的订单号，优先使用
 * @property string $out_trade_no 商户系统内部的订单号
 * @property float $refund_fee 退款总金额，订单总金额，只能为整数
 * @property string $reason 	退款的原因说明
 * @property string $refund_no  商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔(3～24位)
 * @property string $operator_id 商户的操作员编号
 * @property string $terminal_id 商户机具终端编号
 * @property string $store_id  	商户门店编号
 *
 * @package Payment\Common\Ali\Data
 * anthor helei
 */
class RefundData extends AliBaseData
{

    /**
     * 检查退款数据是否正常
     * @author helei
     */
    protected function checkDataParam()
    {
        $tradeNo = $this->trade_no;// 支付宝交易号，查询效率高
        $outTradeNo = $this->out_trade_no;// 商户订单号，查询效率低，不建议使用
        $refundAmount = $this->refund_fee;

        // 二者不能同时为空
        if (empty($outTradeNo) && empty($tradeNo)) {
            throw new PayException('必须提供支付宝交易号或者商户网站唯一订单号。建议使用支付宝交易号');
        }

        if (empty($refundAmount) || ! is_numeric($refundAmount)) {
            throw new PayException('refund_fee 退款的金额，该金额不能大于订单金额,单位为元，支持两位小数');
        }
    }

    protected function buildData()
    {
        $signData = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'charset'       => $this->charset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,

            // 业务参数
            'biz_content'   => $this->getBizContent(),
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @return string
     */
    private function getBizContent()
    {
        $content = [
            'out_trade_no'    => $this->out_trade_no,
            'trade_no'        => $this->trade_no,
            'refund_amount'     => $this->refund_fee,
            'refund_reason'     => $this->reason,
            'out_request_no'    => $this->refund_no,
            'operator_id'       => $this->operator_id,
            'store_id'          => $this->store_id,
            'terminal_id'       => $this->terminal_id,
        ];

        $content = ArrayUtil::paraFilter($content);// 过滤掉空值，下面不用在检查是否为空
        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}
