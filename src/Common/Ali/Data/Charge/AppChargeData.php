<?php
namespace Payment\Common\Ali\Data\Charge;

/**
 * @author: helei
 * @createTime: 2016-07-25 14:49
 * @description: 生成支付宝app 支付所需的请求数据
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class AppChargeData extends ChargeBaseData
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

            // 销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
            'product_code'  => 'QUICK_MSECURITY_PAY',
            'goods_type'    => $this->goods_type,
            'passback_params' => $this->return_param,
            // TODO 优惠信息待支持  业务扩展参数，待支持
            // 'promo_params' => '',
            // 'extend_params => '',
            'disable_pay_channels' => $this->limitPay,
            'store_id' => $this->store_id,
        ];

        $timeExpire = $this->timeout_express;
        if (! empty($timeExpire)) {
            $express = floor(($timeExpire - strtotime($this->timestamp)) / 60);
            ($express > 0) && $content['timeout_express'] = $express . 'm';// 超时时间 统一使用分钟计算
        }

        return $content;
    }
}
