<?php
/**
 * @author: helei
 * @createTime: 2016-06-17 13:09
 * @description:
 */

namespace Payment\Wxpay\Helper;


use Payment\Contracts\DataStruct;

class WxTradeType extends DataStruct
{
    // APP--app支付
    const TYPE_IS_APP = 'APP';

    // NATIVE--原生扫码支付
    const TYPE_IS_NATIVE = 'NATIVE';

    // JSAPI--公众号支付
    const TYPE_IS_JSAPI = 'JSAPI';
}