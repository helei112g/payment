<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:47
 * @description: 支付相关的基础配置  无法被继承
 */

namespace Payment;


final class Config
{
    const VERSION = '2.0-dev';

    // 支付宝 PC 网页支付
    const ALI_CHANNEL_WEB = 'ali_pc_direct';

    // 支付宝 手机网页 支付
    const ALI_CHANNEL_WAP = 'ali_wap';

    /// 支付宝 手机app 支付
    const ALI_CHANNEL_APP = 'ali_app';

    // 微信公众账号 扫码支付  主要用于pc站点
    const WX_CHANNEL_WEB = 'wx_web';

    // 微信 公众账号 支付
    const WX_CHANNEL_PUB = 'wx_pub';

    // 微信 APP 支付
    const WX_CHANNEL_APP = 'wx_app';

    
}