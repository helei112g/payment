<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\CMBank;

use Payment\Contracts\IGatewayRequest;
use Payment\Exceptions\GatewayException;

/**
 * @package Payment\Gateways\CMBank
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2020/1/30 10:38 下午
 * @version : 1.0.0
 * @desc    : app支付
 **/
class AppCharge extends CMBaseObject implements IGatewayRequest
{
    /**
     * app支付不需要请求第三方，签名后返回给客户端
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        return $this->buildParams($requestParams);
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getRequestParams(array $requestParams)
    {
        $nowTime    = time();
        $timeExpire = $requestParams['time_expire'] ?? 0;
        $timeExpire -= $nowTime;
        if ($timeExpire < 3) {
            $timeExpire = 30; // 如果设置不合法，默认改为30
        }

        $params = [
            'dateTime'          => date('YmdHis', $nowTime),
            'branchNo'          => self::$config->get('branch_no', ''),
            'merchantNo'        => self::$config->get('mch_id', ''),
            'date'              => date('Ymd', $requestParams['date'] ?? $nowTime),
            'orderNo'           => $requestParams['trade_no'] ?? '',
            'amount'            => $requestParams['amount'] ?? '', // 固定两位小数，最大11位整数
            'expireTimeSpan'    => $timeExpire, // 分钟
            'payNoticeUrl'      => self::$config->get('notify_url', ''),
            'payNoticePara'     => $requestParams['return_param'] ?? '',
            'clientIP'          => $requestParams['client_ip'] ?? '',
            'cardType'          => self::$config->get('limit_pay', ''), // A:储蓄卡支付，即禁止信用卡支付
            'subMerchantNo'     => $requestParams['sub_mch_id'] ?? '', // 二级商户编码
            'subMerchantName'   => $requestParams['sub_mch_name'] ?? '', // 二级商户名称
            'subMerchantTPCode' => $requestParams['sub_mch_tp_code'] ?? '', // 二级商户类别编码
            'subMerchantTPName' => $requestParams['sub_mch_tp_name'] ?? '', // 二级商户类别名称
            'payModeType'       => $requestParams['mode_type'] ?? '', // 空或00：不设限制； 01：仅不允许微信支付；
            'agrNo'             => $requestParams['agr_no'] ?? '',
            'merchantSerialNo'  => $requestParams['merchant_serial_no'] ?? '',
            'userID'            => $requestParams['user_id'] ?? '',
            'mobile'            => $requestParams['mobile'] ?? '',
            'lon'               => $requestParams['lon'] ?? '',
            'lat'               => $requestParams['lat'] ?? '',
            'riskLevel'         => $requestParams['risk_level'] ?? '',
            'signNoticeUrl'     => self::$config->get('sign_notify_url', ''),
            'signNoticePara'    => $requestParams['return_param'] ?? '',
            //'extendInfo' => '',
            //'extendInfoEncrypType' => '',
        ];

        return $params;
    }
}
