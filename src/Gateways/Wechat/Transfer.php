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
 * @date    : 2019/4/1 8:29 PM
 * @version : 1.0.0
 * @desc    :
 **/
class Transfer extends WechatBaseObject implements IGatewayRequest
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
            'appid'     => '',
            'mch_id'    => '',
            'nonce_str' => '',
            'sign'      => '',
            'sign_type' => '',

            'device_info'      => '',
            'partner_trade_no' => '',
            'openid'           => '',
            'check_name'       => '',
            're_user_name'     => '',
            'spbill_create_ip' => '',

            'amount' => '',
            'desc'   => '',

            'enc_bank_no'   => '',
            'enc_true_name' => '',
            'bank_code'     => '',
        ];
    }
}
