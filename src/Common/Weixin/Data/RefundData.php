<?php
/**
 * @author: helei
 * @createTime: 2016-08-03 15:14
 * @description:
 */

namespace Payment\Common\Weixin\Data;

use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
 * 用户退款
 * Class RefundData
 *
 * @property string $refund_no  商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
 * @property string $transaction_id  微信生成的订单号，在支付通知中有返回
 * @property string $out_trade_no  商户侧传给微信的订单号
 * @property int $total_fee 订单总金额，单位为分，只能为整数
 * @property int $refund_fee 退款总金额，订单总金额，单位为分，只能为整数
 * @property string $operator_id 操作员帐号, 默认为商户号
 *
 * @package Payment\Common\Weixin\Data
 * anthor helei
 */
class RefundData extends WxBaseData
{
    protected function buildData()
    {
        $this->retData = [
            'appid' => $this->appId,
            'mch_id'    => $this->mchId,
            'device_info' => $this->terminal_id,
            'nonce_str' => $this->nonceStr,
            'refund_fee_type' => $this->feeType,
            'transaction_id'    => $this->transaction_id,
            'out_trade_no' => $this->out_trade_no,
            'out_refund_no'  => $this->refund_no,// 商户退款单号
            'total_fee' => $this->total_fee,// 订单总金额
            'refund_fee' => $this->refund_fee,// 退款总金额
            'op_user_id'    => $this->operator_id,//操作员帐号, 默认为商户号
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    /**
     * 检查参数
     * @author helei
     */
    protected function checkDataParam()
    {
        $refundNo = $this->refund_no;// 商户退款单号
        $transactionId = $this->transaction_id;
        $outTradeNo = $this->out_trade_no;
        $totalFee = $this->total_fee;
        $refundFee = $this->refund_fee;
        $operatorId = $this->operator_id;

        // 二者不能同时为空
        if (empty($transactionId) && empty($outTradeNo)) {
            throw new PayException('必须提供微信交易号或商户网站唯一订单号。建议使用微信交易号');
        }

        $this->total_fee = bcmul($totalFee, 100, 0);// 微信以分为单位
        $this->refund_fee = bcmul($refundFee, 100, 0);

        if (bccomp($refundFee, $totalFee, 2) === 1) {
            throw new PayException('退款金额不能大于订单总金额');
        }

        // 该接口，微信配置文件，必须提供cert  key  两个pem文件
        $certPath = $this->appCertPem;
        $keyPath = $this->appKeyPem;
        if (empty($certPath)) {
            throw new PayException('退款接口，必须提供 apiclient_cert.pem 证书');
        }

        if (empty($keyPath)) {
            throw new PayException('退款接口，必须提供 apiclient_key.pem 证书');
        }

        if (empty($operatorId)) {
            $this->operator_id = $this->mchId;
        }
    }
}
