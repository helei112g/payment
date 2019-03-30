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
use Payment\Helpers\StrUtil;
use Payment\Payment;

/**
 * @package Payment\Gateways\Alipay
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/30 3:12 PM
 * @version : 1.0.0
 * @desc    :
 **/
class WebCharge extends AliBaseObject implements IGatewayRequest
{
    const WEB_METHOD = 'alipay.trade.page.pay';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        $params = $this->buildParams($requestParams);

        $params = ArrayUtil::arraySort($params);

        try {
            $signStr = ArrayUtil::createLinkString($params);

            $signType       = self::$config->get('sign_type', '');
            $params['sign'] = $this->makeSign($signType, $signStr);
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new GatewayException($e->getMessage(), Payment::PARAMS_ERR);
        }
        // 支付宝新版本  需要转码
        foreach ($params as &$value) {
            $value = StrUtil::characet($value, 'UTF-8');
        }

        return sprintf('%s?%s', $this->gatewayUrl, http_build_query($params));
    }

    /**
     * 构建请求参数
     * @param array $requestParams
     * @return mixed
     */
    public function buildParams(array $requestParams)
    {
        $timeoutExp = '';
        $timeExpire = intval($requestParams['time_expire']);
        if (!empty($timeExpire)) {
            $expire                      = floor(($timeExpire - time()) / 60);
            ($expire > 0) && $timeoutExp = $expire . 'm';// 超时时间 统一使用分钟计算
        }

        $bizContent = [
            'out_trade_no'    => $requestParams['order_no'] ?? '',
            'product_code'    => $requestParams['product_code'] ?? 'FAST_INSTANT_TRADE_PAY',
            'total_amount'    => $requestParams['amount'] ?? '',
            'subject'         => $requestParams['subject'] ?? '',
            'body'            => $requestParams['body'] ?? '',
            'time_expire'     => $timeExpire ? date('Y-m-d H:i', $timeExpire) : '',
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
            'disable_pay_channels'  => $requestParams['limit_pay'] ?? '',
            'qr_pay_mode'           => $requestParams['qr_pay_mode'] ?? '',
            'qrcode_width'          => $requestParams['qrcode_width'] ?? '',
            'settle_info'           => $requestParams['settle_info'] ?? '',
            'invoice_info'          => $requestParams['invoice_info'] ?? '',
            'agreement_sign_params' => $requestParams['agreement_sign_params'] ?? '',
            'integration_type'      => $requestParams['integration_type'] ?? '',
            'request_from_url'      => $requestParams['request_from_url'] ?? '',
            'business_params'       => $requestParams['business_params'] ?? '',
            'ext_user_info'         => $requestParams['ext_user_info'] ?? '',
        ];
        $bizContent = ArrayUtil::paraFilter($bizContent);

        $requestData = $this->getBaseData(self::WEB_METHOD, $bizContent);
        return ArrayUtil::paraFilter($requestData);
    }
}
