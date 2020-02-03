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

/**
 * @package Payment\Gateways\Alipay
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/30 3:12 PM
 * @version : 1.0.0
 * @desc    : PC场景下单并支付 / 统一收单下单并支付页面接口
 **/
class WebCharge extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.trade.page.pay';

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

            return sprintf('%s?%s', $this->gatewayUrl, http_build_query($params));
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 构建请求参数
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
            'out_trade_no'    => $requestParams['trade_no'] ?? '',
            'product_code'    => 'FAST_INSTANT_TRADE_PAY',
            'total_amount'    => $requestParams['amount'] ?? '',
            'subject'         => $requestParams['subject'] ?? '',
            'body'            => $requestParams['body'] ?? '',
            'time_expire'     => $timeExpire ? date('Y-m-d H:i:s', $timeExpire) : '',
            'goods_detail'    => $requestParams['goods_detail'] ?? '',
            'passback_params' => $requestParams['return_params'] ?? '',
            'extend_params'   => $requestParams['extend_params'] ?? '',
            'goods_type'      => $requestParams['goods_type'] ?? '',
            'timeout_express' => $timeoutExp,
            'promo_params'    => $requestParams['promo_params'] ?? '',
            'royalty_info'    => $requestParams['royalty_info'] ?? '',
            'sub_merchant'    => $requestParams['sub_merchant'] ?? '',
            // 使用禁用列表
            //'enable_pay_channels' => '',
            'store_id'              => $requestParams['store_id'] ?? '',
            'disable_pay_channels'  => implode(self::$config->get('limit_pay', ''), ','),
            'qr_pay_mode'           => $requestParams['qr_pay_mode'] ?? '2',
            'qrcode_width'          => $requestParams['qrcode_width'] ?? '',
            'settle_info'           => $requestParams['settle_info'] ?? '',
            'invoice_info'          => $requestParams['invoice_info'] ?? '',
            'agreement_sign_params' => $requestParams['agreement_sign_params'] ?? '',
            'integration_type'      => $requestParams['integration_type'] ?? 'PCWEB',
            'request_from_url'      => $requestParams['request_from_url'] ?? '',
            'business_params'       => $requestParams['business_params'] ?? '',
            'ext_user_info'         => $requestParams['ext_user_info'] ?? '',
        ];
        $bizContent = ArrayUtil::paraFilter($bizContent);

        return $bizContent;
    }
}
