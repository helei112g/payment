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
 * @date    : 2019/11/26 9:28 PM
 * @version : 1.0.0
 * @desc    : 回退交易的查询
 **/
class ProfitShareRefundQuery extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'pay/profitsharingreturnquery';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        $this->setSignType(self::SIGN_TYPE_SHA);
        try {
            return $this->requestWXApi(self::METHOD, $requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getSelfParams(array $requestParams)
    {
        $selfParams = [
            'order_id'      => $requestParams['order_id'] ?? '',
            'out_order_no'  => $requestParams['out_order_no'] ?? '',
            'out_return_no' => $requestParams['out_return_no'] ?? '',
        ];

        return $selfParams;
    }
}
