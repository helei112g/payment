<?php
namespace Payment\Common\Cmb\Data\Query;

use Payment\Common\Cmb\Data\CmbBaseData;
use Payment\Common\PayException;

/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 下午12:52
 *
 * @property string $date 订单日期,格式：yyyyMMdd
 * @property string $out_trade_no 商户系统内部的订单号
 * @property string $operator_no 商户结账系统的操作员号
 * @property string $type 查询类型,A：按银行订单流水号查询  B：按商户订单日期和订单号查询；
 * @property string $transaction_id 招商的订单号，优先使用
 *
 */
class ChargeQueryData extends CmbBaseData
{

    protected function checkDataParam()
    {
        parent::checkDataParam();

        $bankSerialNo = $this->transaction_id;
        $date = $this->date;
        $orderNo = $this->out_trade_no;

        if (empty($date) || mb_strlen($date) !== 8) {
            throw new PayException('商户订单日期必须提供,格式：yyyyMMdd');
        }

        if ($bankSerialNo && mb_strlen($bankSerialNo) === 20) {
            $this->type = 'A';
        } elseif ($orderNo && mb_strlen($bankSerialNo) <= 32) {
            $this->type = 'B';
        } else {
            throw new PayException('必须设置商户订单信息或者招商流水号');
        }
    }

    protected function getReqData()
    {
        $reqData = [
            'dateTime' => $this->dateTime,
            'branchNo' => $this->branchNo,
            'merchantNo' => $this->merchantNo,
            'type' => $this->type,
            'bankSerialNo' => $this->transaction_id ? $this->transaction_id : '',

            'date' => $this->date ? $this->date : '',
            'orderNo' => $this->out_trade_no ? $this->out_trade_no : '',
            'operatorNo' => $this->operator_no ? $this->operator_no : '',
        ];

        // 这里不能进行过滤空值，招商的空值也要加入签名中
        return $reqData;
    }
}