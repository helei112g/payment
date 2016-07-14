<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:47
 * @description: 支付相关的基础配置  无法被继承
 */

namespace Pyament;


final class Config
{
    /**
     * 支付宝渠道
     */
    const ALI_CHANNEL = 'ali';

    /**
     * 微信渠道
     */
    const WX_CHANNEL = 'wx';
}