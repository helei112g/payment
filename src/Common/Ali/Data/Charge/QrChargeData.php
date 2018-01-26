<?php
namespace Payment\Common\Ali\Data\Charge;

/**
 * 支付宝 扫码支付
 * Class QrChargeData
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * @property string $operator_id  商户操作员编号
 * @property string $terminal_id 商户机具终端编号=
 *
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
            'out_trade_no'  => strval($this->order_no),
            // TODO 卖家支付宝id
            // 'seller_id' => '',
            'total_amount'  => strval($this->amount),
            // TODO 折扣金额
            // 'discountable_amount' => '',
            // TODO  业务扩展参数 订单商品列表信息，待支持
            // 'extend_params => '',
            // 'goods_detail' => '',
            'subject'       => strval($this->subject),
            'body'          => strval($this->body),

            'operator_id' => $this->operator_id,
            'store_id' => $this->store_id,
            'terminal_id' => $this->terminal_id,
        ];

        $timeExpire = $this->timeout_express;
        if (! empty($timeExpire)) {
            $express = floor(($timeExpire - strtotime($this->timestamp)) / 60);
            ($express > 0) && $content['timeout_express'] = $express . 'm';// 超时时间 统一使用分钟计算
        }

        return $content;
    }
}
