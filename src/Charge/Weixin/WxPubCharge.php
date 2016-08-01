<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:28
 * @description: 微信 公众号 支付接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Weixin;

use Payment\Common\Weixin\Data\Charge\WapChargeData;
use Payment\Common\Weixin\WxBaseStrategy;

class WxPubCharge extends WxBaseStrategy
{
    protected function getBuildDataClass()
    {
        return WapChargeData::class;
    }
}