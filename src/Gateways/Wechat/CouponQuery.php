<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\Wechat;

use Payment\Contracts\IGatewayRequest;
use Payment\Exceptions\GatewayException;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/4/7 10:03 AM
 * @version : 1.0.0
 * @desc    :
 **/
class CouponQuery extends WechatBaseObject implements IGatewayRequest
{
    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        // TODO: Implement request() method.
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getSelfParams(array $requestParams)
    {
        $params = [
            'appid'       => '',
            'mch_id'      => '',
            'device_info' => '',
            'nonce_str'   => '',
            'sign'        => '',
            'sign_type'   => '',

            'coupon_id'  => '',
            'openid'     => '',
            'stock_id'   => '',
            'op_user_id' => '',
            'version'    => '',
            'type'       => '',
        ];
    }
}
