<?php
/**
 * @author: helei
 * @createTime: 2016-07-25 14:49
 * @description:
 */

namespace Payment\Common\Ali\Data\Charge;


use Payment\Common\AliConfig;
use Payment\Utils\ArrayUtil;

class AppChargeData extends ChargeBaseData
{
    /**
     * 构建 APP支付 加密数据
     * @author helei
     */
    protected function buildData()
    {
        $timeExpire = $this->timeExpire;

        $signData = [
            // 基本参数
            'service'   => '"' . 'mobile.securitypay.pay' . '"',
            'partner'   => '"' . trim($this->partner) . '"',
            '_input_charset'   => '"' . trim($this->inputCharset) . '"',
            'sign_type'   => '"' . trim($this->signType) . '"',
            'notify_url'    => '"' . trim($this->notifyUrl) . '"',

            // 业务参数
            'out_trade_no'  => '"' . trim($this->order_no) . '"',
            'subject'   => '"' . trim($this->subject) . '"',
            'payment_type'  => '"' . 1 . '"',
            'seller_id' => '"' . trim($this->partner) . '"',
            'total_fee' => '"' . trim($this->amount) . '"',
            'body'  => '"' . trim($this->body) . '"',
            'goods_type'    => '"' . 1 . '"', //默认为实物类型
        ];

        if (! empty($timeExpire)) {
            $signData['it_b_pay'] = '"' . trim($this->timeExpire) . 'm"';// 超时时间 统一使用分钟计算
        }

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}