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
use Payment\Wxpay\WxMobile;

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
                $instance = new WxMobile('APP');
                break;
            default :
                $instance = null;
        }

        return $instance;
    }
}