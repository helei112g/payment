<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 下午5:23
 */

namespace Payment\Common\Cmb\Data\Query;


use Payment\Common\Cmb\Data\CmbBaseData;
use Payment\Common\PayException;

/**
 * Class RefundQueryData
 * @package Payment\Common\Cmb\Data\Query
 *
 * @property string $date 订单日期,格式：yyyyMMdd
 * @property string $out_trade_no 商户系统内部的订单号
 * @property string $type 查询类型,A：按银行退款流水号查单笔  B：按商户订单号+商户退款流水号查单笔  C: 按商户订单号查退款
 * @property string $refund_id 银行退款流水号,长度不超过20位
 * @property string $refund_no  商户侧传给微信的退款单号
 *
 */
class RefundQueryData extends CmbBaseData
{

    protected function checkDataParam()
    {
        parent::checkDataParam();

        $orderNo = $this->out_trade_no;
        $date = $this->date;
        $refundId = $this->refund_id;// 微信的退款交易号
        $refundNo = $this->refund_no;// 商户的退款单号

        if (empty($date)) {
            throw new PayException('商户退款日期，格式：yyyyMMdd');
        }

        if (! empty($refundId)) {// 按银行退款流水号查单笔
            $this->out_trade_no = '';
            $this->refund_no = '';
            $this->type = 'A';
        } elseif (! empty($orderNo) && ! empty($refundNo)) {// 按商户订单号+商户退款流水号查单笔
            $this->refund_id = '';
            $this->type = 'B';
        } elseif (! empty($orderNo)) {// 按商户订单号查退款
            $this->refund_id = '';
            $this->refund_no = '';
            $this->type = 'C';
        } else {
            throw new PayException('请设置需要查询的商户订单号');
        }
    }

    protected function getReqData()
    {
        $reqData = [
            'dateTime' => $this->dateTime,
            'branchNo' => $this->branchNo,
            'merchantNo' => $this->merchantNo,
            'type' => $this->type,
            'orderNo' => $this->out_trade_no ? $this->out_trade_no : '',
            'date' => $this->date,
            'merchantSerialNo' => $this->refund_no ? $this->refund_no : '',
            'bankSerialNo' => $this->refund_id ? $this->refund_id : '',
        ];

        // 这里不能进行过滤空值，招商的空值也要加入签名中
        return $reqData;
    }
}