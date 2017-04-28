<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 下午3:16
 */

namespace Payment\Common\Cmb\Data;

use Payment\Common\PayException;
use Payment\Utils\Rc4Encrypt;

/**
 * 退款API
 * Class RefundData
 * @package Payment\Common\Cmb\Data
 *
 * @property string $date 订单日期,格式：yyyyMMdd
 * @property string $out_trade_no 商户系统内部的订单号
 * @property string $refund_no  商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
 * @property float $refund_fee 退款总金额，订单总金额，只能为整数
 * @property string $reason 	退款的原因说明
 * @property string $operator_id 商户的操作员编号
 *
 */
class RefundData extends CmbBaseData
{

    protected function checkDataParam()
    {
        parent::checkDataParam();

        $refundNo = $this->refund_no;// 商户退款单号
        $date = $this->date;// 商户订单日期，支付时的订单日期  格式：yyyyMMdd
        $outTradeNo = $this->out_trade_no;
        $refundFee = $this->refund_fee;
        $operatorId = $this->operator_id;

        if (empty($date) || mb_strlen($date) !== 8) {
            throw new PayException('商户订单日期必须提供,格式：yyyyMMdd');
        }

        if (empty($outTradeNo)) {
            throw new PayException('必须提供商户网站唯一订单号。');
        }

        if (empty($refundNo) && mb_strlen($refundNo) < 21) {
            throw new PayException('退款流水号,商户生成，不能超过20位');
        }

        if (empty($refundFee) || ! is_numeric($refundFee)) {
            throw new PayException('退款金额,格式xxxx.xx');
        }

        if (empty($operatorId)) {
            throw new PayException('必须提供 商户结账系统的操作员号');
        }
    }

    protected function getReqData()
    {
        $rc4 = new Rc4Encrypt($this->merKey);

        $reqData = [
            'dateTime' => $this->dateTime,
            'branchNo' => $this->branchNo,
            'merchantNo' => $this->merchantNo,
            'date' => $this->date,
            'orderNo' => $this->out_trade_no,
            'refundSerialNo' => trim($this->refund_no),
            'amount' => $this->refund_fee,
            'desc' => $this->reason,
            'operatorNo' => $this->operator_id,
            'encrypType' => 'RC4',// 这里不让用户控制，直接采用 rc4加密
            'pwd' => $rc4->encrypt($this->opPwd),
        ];

        // 这里不能进行过滤空值，招商的空值也要加入签名中
        return $reqData;
    }
}