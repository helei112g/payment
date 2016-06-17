<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 21:18
 * @description: 支付相关操作的工厂类
 */

namespace Payment\Factory;



use Payment\Alipay\AlipayDirect;
use Payment\Alipay\AlipayMobile;
use Payment\Common\ChargeChannel;
use Payment\Wxpay\Helper\WxTradeType;
use Payment\Wxpay\WxPubPay;

class ChargeFactory
{
    public static function getInstance($channel)
    {
        $instance = null;
        switch ($channel) {
            case ChargeChannel::CHANNEL_IS_ALIPAY_DIRECT:
                $instance = new AlipayDirect();
                break;
            case ChargeChannel::CHANNEL_IS_ALIPAY:
                $instance = new AlipayMobile();
                break;
            case ChargeChannel::CHANNEL_IS_WX;
                $instance = new WxPubPay(WxTradeType::TYPE_IS_APP);
                break;
            case ChargeChannel::CHANNEL_IS_WX_PUB;// 微信公众号支付
                $instance = new WxPubPay(WxTradeType::TYPE_IS_JSAPI);
                break;
            case ChargeChannel::CHANNEL_IS_WX_QR;// 微信二维码支付
                $instance = new WxPubPay(WxTradeType::TYPE_IS_NATIVE);
                break;
            default :
                $instance = null;
        }

        return $instance;
    }
}