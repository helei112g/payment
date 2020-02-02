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
 * @date    : 2019/3/28 10:21 PM
 * @version : 1.0.0
 * @desc    : app 支付
 **/
class AppCharge extends AliBaseObject implements IGatewayRequest
{
    // 这个操作是在客户端发起的，服务端只负责组装参数
    const METHOD = 'alipay.trade.app.pay';

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
            return http_build_query($params);
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
            'timeout_express' => $timeoutExp,
            'total_amount'    => $requestParams['amount'] ?? '',
            'product_code'    => $requestParams['product_code'] ?? '',
            'body'            => $requestParams['body'] ?? '',
            'subject'         => $requestParams['subject'] ?? '',
            'out_trade_no'    => $requestParams['trade_no'] ?? '',
            'time_expire'     => $timeExpire ? date('Y-m-d H:i', $timeExpire) : '',
            'goods_type'      => $requestParams['goods_type'] ?? '',
            'promo_params'    => $requestParams['promo_params'] ?? '',
            'passback_params' => urlencode($requestParams['return_params'] ?? ''),
            'extend_params'   => $requestParams['extend_params'] ?? '',
            // 使用禁用列表
            //'enable_pay_channels' => '',
            'store_id'             => $requestParams['store_id'] ?? '',
            'specified_channel'    => 'pcredit',
            'disable_pay_channels' => implode(self::$config->get('limit_pay', ''), ','),
            'ext_user_info'        => $requestParams['ext_user_info'] ?? '',
            'business_params'      => $requestParams['business_params'] ?? '',
        ];
        $bizContent = ArrayUtil::paraFilter($bizContent);

        return $bizContent;
    }
}
