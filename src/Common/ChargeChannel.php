<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 21:20
 * @description: 支付使用的第三方支付渠道 可取值常量
 */

namespace Payment\Common;


class ChargeChannel
{
    // 支付宝 PC 网页支付 (即时到帐接口)
    const CHANNEL_IS_ALIPAY_DIRECT = 'ali_direct';

    // 支付宝手机支付
    const CHANNEL_IS_ALIPAY = 'alipay';

    // 微信支付 (APP支付接口)
    const CHANNEL_IS_WX = 'wx';

    // 微信公众号内H5支付
    const CHANNEL_IS_WX_PUB = 'wx_pub';

    // 微信扫码支付 (尚未开通)
    const CHANNEL_IS_WX_QR = 'wx_qr';
}