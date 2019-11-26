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
            'out_trade_no'    => $requestParams['out_trade_no'] ?? '',
            'transaction_id'  => $requestParams['transaction_id'] ?? '',
            'out_refund_no'   => $requestParams['out_refund_no'] ?? '',
            'total_fee'       => $requestParams['total_fee'] ?? '',
            'refund_fee'      => $requestParams['refund_fee'] ?? '',
            'refund_fee_type' => $requestParams['refund_fee_type'] ?? 'CNY',
            'refund_desc'     => $requestParams['refund_desc'] ?? '',
            'refund_account'  => $requestParams['refund_account'] ?? 'REFUND_SOURCE_REC',
            'notify_url'      => self::$config->get('notify_url', ''),
        ];

        return $selfParams;
    }
}
