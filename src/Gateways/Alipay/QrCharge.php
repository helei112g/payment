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
 * @desc    : 收银员通过收银台或商户后台调用支付宝接口，生成二维码后，展示给用户，由用户扫描二维码完成订单支付。
 **/
class QrCharge extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.trade.precreate';

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
            'seller_id'            => $requestParams['seller_id'] ?? '',
            'total_amount'         => $requestParams['amount'] ?? '',
            'discountable_amount'  => $requestParams['discountable_amount'] ?? '',
            'subject'              => $requestParams['subject'] ?? '',
            'goods_detail'         => $requestParams['goods_detail'] ?? '',
            'body'                 => $requestParams['body'] ?? '',
            'operator_id'          => $requestParams['operator_id'] ?? '',
            'store_id'             => $requestParams['store_id'] ?? '',
            'disable_pay_channels' => implode(self::$config->get('limit_pay', ''), ','),
            // 使用禁用列表
            //'enable_pay_channels' => '',
            'terminal_id'             => $requestParams['terminal_id'] ?? '',
            'extend_params'           => $requestParams['extend_params'] ?? '',
            'timeout_express'         => $timeoutExp,
            'settle_info'             => $requestParams['settle_info'] ?? '',
            'merchant_order_no'       => $requestParams['merchant_order_no'] ?? '',
            'business_params'         => $requestParams['business_params'] ?? '',
            'qr_code_timeout_express' => $timeoutExp,
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
                throw new GatewayException(sprintf('format qr data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_trade_precreate_response'];
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
