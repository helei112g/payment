<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\CMBank;

use Payment\Contracts\IGatewayRequest;
use Payment\Exceptions\GatewayException;

/**
 * @package Payment\Gateways\CMBank
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/11/27 7:47 PM
 * @version : 1.0.0
 * @desc    : 支付协议查询: 查询客户一网通支付协议是否已经成功签署，如果银行未正常返回客户的签约结果，商户可通过该接口主动发起查询
 **/
class ProtocolQuery extends CMBaseObject implements IGatewayRequest
{
    const METHOD = 'CmbBank_B2B/UI/NetPay/DoBusiness.ashx';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        $this->gatewayUrl = 'https://b2b.cmbchina.com/%s';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'http://121.15.180.72/%s';
        }
        try {
            return $this->requestCMBApi(self::METHOD, $requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getRequestParams(array $requestParams)
    {
        $nowTime = time();

        $params = [
            'dateTime'         => date('YmdHis', $nowTime),
            'txCode'           => 'CMCX',
            'branchNo'         => self::$config->get('branch_no', ''),
            'merchantNo'       => self::$config->get('mch_id', ''),
            'merchantSerialNo' => $requestParams['merchant_serial_no'] ?? '',
            'agrNo'            => $requestParams['agr_no'] ?? '',
        ];

        return $params;
    }
}
