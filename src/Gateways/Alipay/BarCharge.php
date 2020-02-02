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
 * @date    : 2019/3/30 3:12 PM
 * @version : 1.0.0
 * @desc    : 收银员使用扫码设备读取用户手机支付宝“付款码”
 **/
class BarCharge extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.trade.pay';

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getBizContent(array $requestParams)
    {
        $timeoutExp = '';
        $timeExpire = intval($requestParams['time_expire']);
        if (!empty($timeExpire)) {
            $expire                      = floor(($timeExpire - time()) / 60);
            ($expire > 0) && $timeoutExp = $expire . 'm';// 超时时间 统一使用分钟计算
        }

        $bizContent = [
            'out_trade_no'         => $requestParams['trade_no'] ?? '',
            'scene'                => $requestParams['scene'] ?? 'bar_code',
            'auth_code'            => $requestParams['auth_code'] ?? '',
            'product_code'         => $requestParams['product_code'] ?? '',
            'subject'              => $requestParams['subject'] ?? '',
            'buyer_id'             => $requestParams['buyer_id'] ?? '',
            'seller_id'            => $requestParams['seller_id'] ?? '',
            'total_amount'         => $requestParams['amount'] ?? '',
            'trans_currency'       => self::$config->get('fee_type', 'CNY'),
            'settle_currency'      => $requestParams['settle_currency'] ?? 'CNY',
            'discountable_amount'  => $requestParams['discountable_amount'] ?? '',
            'body'                 => $requestParams['body'] ?? '',
            'goods_detail'         => $requestParams['goods_detail'] ?? '',
            'operator_id'          => $requestParams['operator_id'] ?? '',
            'store_id'             => $requestParams['store_id'] ?? '',
            'terminal_id'          => $requestParams['terminal_id'] ?? '',
            'extend_params'        => $requestParams['extend_params'] ?? '',
            'timeout_express'      => $timeoutExp,
            'auth_confirm_mode'    => $requestParams['auth_confirm_mode'] ?? '',
            'terminal_params'      => $requestParams['terminal_params'] ?? '',
            'promo_params'         => $requestParams['promo_params'] ?? '',
            'advance_payment_type' => $requestParams['advance_payment_type'] ?? '',
            //'is_async_pay'         => $requestParams['is_async_pay'] ?? false,
        ];
        $bizContent = ArrayUtil::paraFilter($bizContent);

        return $bizContent;
    }

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws \Payment\Exceptions\GatewayException
     */
    public function request(array $requestParams)
    {
        try {
            $params = $this->buildParams(self::METHOD, $requestParams);
            $ret    = $this->get($this->gatewayUrl, $params);
            $retArr = json_decode($ret, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new GatewayException(sprintf('format bar data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, $ret);
            }

            $content = $retArr['alipay_trade_pay_response'];
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
