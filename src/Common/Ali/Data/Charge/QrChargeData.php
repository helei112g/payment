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
 *
 * @property string $operator_id  商户操作员编号
 * @property string $terminal_id 商户机具终端编号
 * @property string $alipay_store_id 支付宝店铺的门店ID
 *
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
            'operator_id' => $this->operator_id,
            'terminal_id' => $this->terminal_id,
            'alipay_store_id' => $this->alipay_store_id,
        ];

        $timeExpire = $this->timeout_express;
        if (! empty($timeExpire)) {
            $express = floor(($timeExpire - strtotime($this->timestamp)) / 60);
            $express && $content['timeout_express'] = $express . 'm';// 超时时间 统一使用分钟计算
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}
