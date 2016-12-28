<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:29
 * @description: 微信 扫码支付  主要用于网站上
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Weixin;

use Payment\Common\Weixin\Data\Charge\QrChargeData;
use Payment\Common\Weixin\WxBaseStrategy;

class WxQrCharge extends WxBaseStrategy
{

    protected function getBuildDataClass()
    {
        return QrChargeData::class;
    }

    /**
     * 处理扫码支付的返回值
     * @param array $ret
     * @return string  可生产二维码的uri
     * @author helei
     */
    protected function retData(array $ret)
    {
        // 扫码支付，返回链接
        return $ret['code_url'];
    }
}