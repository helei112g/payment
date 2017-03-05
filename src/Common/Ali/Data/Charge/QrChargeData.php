<?php
/**
 * Created by PhpStorm.
 * User: helei  <dayugog@gmail.com>
 * Date: 2016/12/28
 * Time: 20:24
 */

namespace Payment\Common\Ali\Data\Charge;

/**
 * 支付宝 扫码支付
 * Class QrChargeData
 * @package Payment\Common\Ali\Data\Charge
 */
class QrChargeData extends ChargeBaseData
{
    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @return string
     */
    protected function getBizContent()
    {
        $content = [
            'body'          => strval($this->body),
            'subject'       => strval($this->subject),
            'out_trade_no'  => strval($this->order_no),
            'total_amount'  => strval($this->amount),
            'seller_id' => $this->partner,

            'store_id' => $this->store_id,
        ];

        $timeExpire = $this->timeout_express;
        if (! empty($timeExpire)) {
            $express = floor(($timeExpire - strtotime($this->timestamp)) / 60);
            $express && $content['it_b_pay'] = $express . 'm';// 超时时间 统一使用分钟计算
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}
