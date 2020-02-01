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
use Payment\Payment;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/4/1 8:27 PM
 * @version : 1.0.0
 * @desc    : 申请退款
 **/
class Refund extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'secapi/pay/refund';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
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
        $totalFee   = bcmul($requestParams['total_fee'], 100, 0);
        $refundFee  = bcmul($requestParams['refund_fee'], 100, 0);
        $selfParams = [
            'transaction_id'  => $requestParams['transaction_id'] ?? '',
            'out_trade_no'    => $requestParams['trade_no'] ?? '',
            'out_refund_no'   => $requestParams['refund_no'] ?? '',
            'total_fee'       => $totalFee,
            'refund_fee'      => $refundFee,
            'refund_fee_type' => self::$config->get('fee_type', 'CNY'),
            'refund_desc'     => $requestParams['refund_desc'] ?? '',
            'refund_account'  => $requestParams['refund_account'] ?? 'REFUND_SOURCE_RECHARGE_FUNDS', // REFUND_SOURCE_UNSETTLED_FUNDS
            'notify_url'      => self::$config->get('notify_url', ''),
        ];

        return $selfParams;
    }
}
