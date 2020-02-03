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
 * @desc    : 二维码支付
 **/
class QrCharge extends CMBaseObject implements IGatewayRequest
{
    const METHOD = 'netpayment/BaseHttp.dll?MB_APPQRPay';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        $this->gatewayUrl = 'https://netpay.cmbchina.com/%s';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'http://121.15.180.66:801/%s';
        }

        try {
            return $this->requestCMBApi(self::METHOD, $requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getRequestParams(array $requestParams)
    {
        $nowTime    = time();
        $timeExpire = $requestParams['time_expire'] ?? 0;
        $timeExpire = $timeExpire - $nowTime;
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
            'expireTimeSpan'    => $timeExpire,
            'payNoticeUrl'      => self::$config->get('notify_url', ''),
            'payNoticePara'     => $requestParams['return_param'] ?? '',
            'returnUrl'         => self::$config->get('return_url', ''),
            'clientIP'          => $requestParams['client_ip'] ?? '',
            'cardType'          => self::$config->get('limit_pay', ''), // A:储蓄卡支付，即禁止信用卡支付
            'subMerchantNo'     => $requestParams['sub_mch_id'] ?? '', // 二级商户编码
            'subMerchantName'   => $requestParams['sub_mch_name'] ?? '', // 二级商户名称
            'subMerchantTPCode' => $requestParams['sub_mch_tp_code'] ?? '', // 二级商户类别编码
            'subMerchantTPName' => $requestParams['sub_mch_tp_name'] ?? '', // 二级商户类别名称
            //'encrypType' => '',
            //'encrypData' => '',
        ];

        return $params;
    }
}
