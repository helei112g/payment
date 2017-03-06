<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 18:05
 * @description:
 */

namespace Payment\Common\Weixin\Data\Charge;

use Payment\Utils\ArrayUtil;

/**
 * Class AppChargeData
 * 微信APP支付
 * @package Payment\Common\Weixin\Data\Charge
 */
class AppChargeData extends ChargeBaseData
{
    protected function buildData()
    {
        $signData = [
            // 基本数据
            'appid' => trim($this->appId),
            'mch_id'    => trim($this->mchId),
            'nonce_str' => $this->nonceStr,
            'sign_type' => $this->signType,
            'fee_type'  => $this->feeType,
            'notify_url'    => $this->notifyUrl,
            'trade_type'    => $this->tradeType, //设置APP支付
            'limit_pay' => $this->limitPay,  // 指定不使用信用卡

            // 业务数据
            'device_info'   => $this->terminal_id,
            'body'  => trim($this->subject),
            //'detail' => json_encode($this->body, JSON_UNESCAPED_UNICODE);
            'attach'    => trim($this->return_param),
            'out_trade_no'  => trim($this->order_no),
            'total_fee' => $this->amount,
            'spbill_create_ip'  => trim($this->client_ip),
            'time_start'    => $this->timeStart,
            'time_expire'   => $this->timeout_express,
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}
