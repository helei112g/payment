<?php

namespace Payment\Common\Ali\Data;

use Payment\Common\PayException;

/**
 * Class CancelData
 *
 * @property string $trade_no 支付宝的订单号，优先使用
 * @property string $out_trade_no 商户系统内部的订单号
 *
 * @package Payment\Common\Ali\Data
 * anthor dong
 */
class CancelData extends AliBaseData
{

    /**
     * 检查退款数据是否正常
     * @author dong
     */
    protected function checkDataParam()
    {
        $tradeNo = $this->trade_no;// 支付宝交易号，查询效率高
        $outTradeNo = $this->out_trade_no;// 商户订单号，查询效率低，不建议使用

        // 二者不能同时为空
        if (empty($outTradeNo) && empty($tradeNo)) {
            throw new PayException('必须提供支付宝交易号或者商户网站唯一订单号。建议使用支付宝交易号');
        }

    }

    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @return string
     */
    protected function getBizContent()
    {
        $content = [
            'out_trade_no' => $this->out_trade_no,
            'trade_no' => $this->trade_no,
        ];

        return $content;
    }
}
