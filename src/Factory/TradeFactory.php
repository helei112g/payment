<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 12:30
 * @description:
 */

namespace Payment\Factory;



use Payment\Alipay\AliTradeApi;
use Payment\Common\ChargeChannel;
use Payment\Wxpay\WxTradeApi;

class TradeFactory
{
    public static function getInstance($channel)
    {
        $instance = null;
        switch ($channel) {
            case ChargeChannel::CHANNEL_IS_ALIPAY:
            case ChargeChannel::CHANNEL_IS_ALIPAY_DIRECT:
                $instance = new AliTradeApi();
                break;
            case ChargeChannel::CHANNEL_IS_WX:
            case ChargeChannel::CHANNEL_IS_WX_QR:
                $instance = new WxTradeApi();
                break;
            default :
                $instance = null;
        }

        return $instance;
    }
}