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
            'device_info'   => 'WEB',
            'nonce_str' => $this->nonceStr,
            'fee_type'  => $this->feeType,
            'notify_url'    => $this->notifyUrl,
            'time_start'    => $this->timeStart,
            'time_expire'   => $this->timeExpire,
            //'limit_pay' => 'no_credit',  // 指定不使用信用卡

            // 业务数据
            'body'  => trim($this->body),
            'attach'    => trim($this->extra_param),
            'out_trade_no'  => trim($this->order_no),
            'total_fee' => $this->amount,
            'spbill_create_ip'  => trim($this->client_ip),
            'trade_type'    => 'APP', //设置APP支付
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}