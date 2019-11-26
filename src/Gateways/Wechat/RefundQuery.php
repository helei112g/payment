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
use Payment\Helpers\DataParser;
use Payment\Payment;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/4/1 8:28 PM
 * @version : 1.0.0
 * @desc    :
 **/
class RefundQuery extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'pay/refundquery';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        try {
            $xmlData = $this->buildParams($requestParams);
            $url     = sprintf($this->gatewayUrl, self::METHOD);

            $this->setHttpOptions($this->getCertOptions());
            $resXml = $this->postXML($url, $xmlData);

            $resArr = DataParser::toArray($resXml);
            if ($resArr['return_code'] !== self::REQ_SUC) {
                throw new GatewayException($resArr['retmsg'], Payment::GATEWAY_REFUSE, $resArr);
            }

            return $resArr;
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
            'transaction_id' => $requestParams['transaction_id'] ?? '',
            'out_trade_no'   => $requestParams['out_trade_no'] ?? '',
            'out_refund_no'  => $requestParams['out_refund_no'] ?? '',
            'refund_id'      => $requestParams['refund_id'] ?? '',
            'offset'         => $requestParams['offset'] ?? '',
        ];

        return $selfParams;
    }
}
