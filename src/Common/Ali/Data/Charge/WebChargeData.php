<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:28
 * @description: 即时到帐 接口的数据处理类
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common\Ali\Data\Charge;

use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
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
            'body'          => strval($this->body),
            'subject'       => strval($this->subject),
            'out_trade_no'  => strval($this->order_no),
            'total_amount'  => strval($this->amount),

            // 销售产品码，商家和支付宝签约的产品码，为固定值QUICK_WAP_PAY
            'product_code'  => 'FAST_INSTANT_TRADE_PAY',
            'goods_type'    => $this->goods_type,
            'passback_params' => $this->return_param,
            'disable_pay_channels' => $this->limitPay,
            'store_id' => $this->store_id,
            'qr_pay_mode'   => $this->qr_mod,
        ];

        $timeExpire = $this->timeout_express;
        if (! empty($timeExpire)) {
            $express = floor(($timeExpire - strtotime($this->timestamp)) / 60);
            $express && $content['timeout_express'] = $express . 'm';// 超时时间 统一使用分钟计算
        }

        $content = ArrayUtil::paraFilter($content);// 过滤掉空值，下面不用在检查是否为空
        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}
