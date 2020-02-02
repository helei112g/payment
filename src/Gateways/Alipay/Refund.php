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
 * @date    : 2019/3/31 9:12 AM
 * @version : 1.0.0
 * @desc    : 统一收单交易退款接口
 **/
class Refund extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.trade.refund';

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getBizContent(array $requestParams)
    {
        $bizContent = [
            'out_trade_no'              => $requestParams['trade_no'] ?? '',
            'trade_no'                  => $requestParams['transaction_id'] ?? '',
            'refund_amount'             => $requestParams['refund_fee'] ?? '',
            'refund_currency'           => $requestParams['refund_currency'] ?? 'CNY',
            'refund_reason'             => $requestParams['reason'] ?? '',
            'out_request_no'            => $requestParams['refund_no'] ?? '',
            'operator_id'               => $requestParams['operator_id'] ?? '',
            'store_id'                  => $requestParams['store_id'] ?? '',
            'terminal_id'               => $requestParams['terminal_id'] ?? '',
            'goods_detail'              => $requestParams['goods_detail'] ?? '',
            'refund_royalty_parameters' => $requestParams['refund_royalty_parameters'] ?? '',
            'org_pid'                   => $requestParams['org_pid'] ?? '',
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
                throw new GatewayException(sprintf('format refund data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_trade_refund_response'];
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
