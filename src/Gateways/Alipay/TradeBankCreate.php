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
 * @date    : 2019/11/26 9:36 PM
 * @version : 1.0.0
 * @desc    : 网商银行全渠道收单业务订单创建
 **/
class TradeBankCreate extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'mybank.payment.trade.order.create';

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getBizContent(array $requestParams)
    {
        $bizContent = [
            'partner_id'       => $requestParams['partner_id'] ?? '',
            'out_trade_no'     => $requestParams['trade_no'] ?? '',
            'recon_related_no' => $requestParams['recon_related_no'] ?? '',
            'pd_code'          => $requestParams['pd_code'] ?? '',
            'ev_code'          => $requestParams['ev_code'] ?? '',
            'total_amount'     => $requestParams['total_amount'] ?? '',
            'currency_code'    => $requestParams['currency_code'] ?? '',
            'goods_info'       => $requestParams['goods_info'] ?? '',
            'seller_id'        => $requestParams['seller_id'] ?? '',
            'pay_type'         => $requestParams['pay_type'] ?? '',
            'pay_date'         => $requestParams['pay_date'] ?? '',
            'remark'           => $requestParams['remark'] ?? '',
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
                throw new GatewayException(sprintf('format trade bank create get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['mybank_payment_trade_order_create_response'];
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
