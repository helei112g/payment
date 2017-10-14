<?php
namespace Payment\Common\Ali\Data\Charge;

/**
 * @author: helei
 * @createTime: 2016-07-15 17:28
 * @description: 即时到帐 接口的数据处理类
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class WebChargeData
 *
 * @inheritdoc
 * @property integer $qr_mod
 *
 * @package Payment\Charge\Ali\Data
 * anthor helei
 */
class WebChargeData extends ChargeBaseData
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
            // 销售产品码，商家和支付宝签约的产品码，为固定值QUICK_WAP_PAY
            'product_code'  => 'FAST_INSTANT_TRADE_PAY',
            'total_amount'  => strval($this->amount),
            'subject'       => strval($this->subject),
            'body'          => strval($this->body),
            // TODO 订单包含的商品列表信息 待实现
            // 'goods_detail' => '',
            'passback_params' => $this->return_param,
            // TODO 业务扩展参数，待支持
            // 'extend_params => '',
            'goods_type'    => $this->goods_type,
            'disable_pay_channels' => $this->limitPay,
            'store_id' => $this->store_id,
            'qr_pay_mode'   => $this->qr_mod,
            // TODO 设置二维码宽度
            // 'qrcode_width' => '',// qr_pay_mode = 4时有效。设置二维码宽度
        ];

        $timeExpire = $this->timeout_express;
        if (! empty($timeExpire)) {
            $express = floor(($timeExpire - strtotime($this->timestamp)) / 60);
            ($express > 0) && $content['timeout_express'] = $express . 'm';// 超时时间 统一使用分钟计算
        }

        return $content;
    }
}
