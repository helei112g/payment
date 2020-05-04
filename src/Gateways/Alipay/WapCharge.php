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
 * @desc    : 手机网站支付接口2.0
 **/
class WapCharge extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.trade.wap.pay';

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
            'body'            => $requestParams['body'] ?? '',
            'subject'         => $requestParams['subject'] ?? '',
            'out_trade_no'    => $requestParams['trade_no'] ?? '',
            'timeout_express' => $timeoutExp,
            'time_expire'     => $timeExpire ? date('Y-m-d H:i', $timeExpire) : '',
            'total_amount'    => $requestParams['amount'] ?? '',
            'auth_token'      => $requestParams['auth_token'] ?? '',
            'goods_type'      => $requestParams['goods_type'] ?? '',
            'passback_params' => $requestParams['return_params'] ?? '',
            'quit_url'        => $requestParams['quit_url'] ?? '',
            'product_code'    => 'QUICK_WAP_WAY',
            'promo_params'    => $requestParams['promo_params'] ?? '',
            'extend_params'   => $requestParams['extend_params'] ?? '',
            // 使用禁用列表
            //'enable_pay_channels' => '',
            'disable_pay_channels' => implode(self::$config->get('limit_pay', ''), ','),
            'store_id'             => $requestParams['store_id'] ?? '',
            'specified_channel'    => $requestParams['specified_channel'] ?? 'pcredit', //支付宝原因，当前仅支持 pcredit
            'business_params'      => $requestParams['business_params'] ?? '',
            'ext_user_info'        => $requestParams['ext_user_info'] ?? '',
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

            return sprintf('%s?%s', $this->gatewayUrl, http_build_query($params));
        } catch (GatewayException $e) {
            throw $e;
        }
    }
}
