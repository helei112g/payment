<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:29
 * @description: 微信 扫码支付  主要用于网站上
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Weixin;

use Payment\Common\Weixin\Data\Charge\WebChargeData;
use Payment\Common\Weixin\WxBaseStrategy;

class WxWebCharge extends WxBaseStrategy
{

    protected function getBuildDataClass()
    {
        return WebChargeData::class;
    }
}