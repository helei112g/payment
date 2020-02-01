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
 * @date    : 2019/11/26 8:46 PM
 * @version : 1.0.0
 * @desc    : 企业付款到银行卡API
 **/
class TransferBank extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'mmpaysptrans/pay_bank';

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getSelfParams(array $requestParams)
    {
        $totalFee = bcmul($requestParams['amount'], 100, 0);

        $selfParams = [
            'partner_trade_no' => $requestParams['trans_no'] ?? '',
            'enc_bank_no'      => $requestParams['enc_bank_no'] ?? '',
            'enc_true_name'    => $requestParams['enc_true_name'] ?? '',
            'bank_code'        => $requestParams['bank_code'] ?? '',
            'amount'           => $totalFee,
            'desc'             => $requestParams['desc'] ?? '',
        ];

        return $selfParams;
    }

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
}
