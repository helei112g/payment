<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\Alipay;

use Payment\Contracts\IGatewayRequest;
use Payment\Exceptions\GatewayException;
use Payment\Helpers\ArrayUtil;
use Payment\Payment;

/**
 * @package Payment\Gateways\Alipay
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/11/26 9:33 PM
 * @version : 1.0.0
 * @desc    : 统一收单交易创建接口
 **/
class TradeCreate extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.trade.create';

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getBizContent(array $requestParams)
    {
        $bizContent = [
            'out_trade_no'          => $requestParams['trade_no'] ?? '',
            'seller_id'             => $requestParams['seller_id'] ?? '',
            'total_amount'          => $requestParams['total_amount'] ?? '',
            'discountable_amount'   => $requestParams['discountable_amount'] ?? '',
            'subject'               => $requestParams['subject'] ?? '',
            'body'                  => $requestParams['body'] ?? '',
            'buyer_id'              => $requestParams['buyer_id'] ?? '',
            'goods_detail'          => $requestParams['goods_detail'] ?? '',
            'product_code'          => $requestParams['product_code'] ?? '',
            'operator_id'           => $requestParams['operator_id'] ?? '',
            'store_id'              => $requestParams['store_id'] ?? '',
            'terminal_id'           => $requestParams['terminal_id'] ?? '',
            'extend_params'         => $requestParams['extend_params'] ?? '',
            'timeout_express'       => $requestParams['timeout_express'] ?? '',
            'settle_info'           => $requestParams['settle_info'] ?? '',
            'logistics_detail'      => $requestParams['logistics_detail'] ?? '',
            'business_params'       => $requestParams['business_params'] ?? '',
            'receiver_address_info' => $requestParams['receiver_address_info'] ?? '',
        ];
        $bizContent = ArrayUtil::paraFilter($bizContent);

        return $bizContent;
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
            $params = $this->buildParams(self::METHOD, $requestParams);
            $ret    = $this->get($this->gatewayUrl, $params);
            $retArr = json_decode($ret, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new GatewayException(sprintf('format trade create get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_trade_create_response'];
            if ($content['code'] !== self::REQ_SUC) {
                throw new GatewayException(sprintf('request get failed, msg[%s], sub_msg[%s]', $content['msg'], $content['sub_msg']), Payment::SIGN_ERR, $content);
            }

            $signFlag = $this->verifySign($content, $retArr['sign']);
            if (!$signFlag) {
                throw new GatewayException('check sign failed', Payment::SIGN_ERR, $retArr);
            }

            return $content;
        } catch (GatewayException $e) {
            throw $e;
        }
    }
}
