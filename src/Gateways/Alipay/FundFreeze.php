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
 * @date    : 2019/11/25 7:01 PM
 * @version : 1.0.0
 * @desc    : 收银员使用扫码设备读取用户支付宝钱包“付款码”后，将条码信息和订单信息通过本接口上送至支付宝发起资金冻结
 **/
class FundFreeze extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.fund.auth.order.freeze';

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getBizContent(array $requestParams)
    {
        $bizContent = [
            'auth_code'           => $requestParams['auth_code'] ?? '',
            'auth_code_type'      => $requestParams['auth_code_type'] ?? '',
            'out_order_no'        => $requestParams['out_order_no'] ?? '',
            'out_request_no'      => $requestParams['out_request_no'] ?? '',
            'order_title'         => $requestParams['order_title'] ?? '',
            'amount'              => $requestParams['amount'] ?? '',
            'payee_logon_id'      => $requestParams['payee_logon_id'] ?? '',
            'payee_user_id'       => $requestParams['payee_user_id'] ?? '',
            'pay_timeout'         => $requestParams['pay_timeout'] ?? '',
            'extra_param'         => $requestParams['extra_param'] ?? '',
            'product_code'        => $requestParams['product_code'] ?? '',
            'trans_currency'      => $requestParams['trans_currency'] ?? '',
            'settle_currency'     => $requestParams['settle_currency'] ?? '',
            'scene_code'          => $requestParams['scene_code'] ?? '',
            'terminal_params'     => $requestParams['terminal_params'] ?? '',
            'enable_pay_channels' => $requestParams['enable_pay_channels'] ?? '',
            'identity_params'     => $requestParams['identity_params'] ?? '',
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
                throw new GatewayException(sprintf('format fund freeze data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_fund_auth_order_freeze_response'];
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
