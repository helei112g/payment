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
 * @date    : 2019/11/27 6:58 PM
 * @version : 1.0.0
 * @desc    : 签约: 单独的签约功能，商户如需向客户提供单独的签约功能，可通过该接口实现。
 **/
class BindCard extends CMBaseObject implements IGatewayRequest
{
    const ONLINE_METHOD = 'https://mobile.cmbchina.com/mobilehtml/DebitCard/M_NetPay/OneNetRegister/NP_BindCard.aspx';

    const SANDBOX_METHOD = 'http://121.15.180.66:801/mobilehtml/DebitCard/M_NetPay/OneNetRegister/NP_BindCard.aspx';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        // 初始 网关地址
        $this->setGatewayUrl(self::ONLINE_METHOD);
        if ($this->isSandbox) {
            $this->setGatewayUrl(self::SANDBOX_METHOD);
        }
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getRequestParams(array $requestParams)
    {
        $nowTime = time();

        $params = [
            'dateTime'         => date('YmdHis', $nowTime),
            'merchantSerialNo' => $requestParams['merchant_serial_no'] ?? '',
            'agrNo'            => $requestParams['agr_no'] ?? '',
            'branchNo'         => self::$config->get('branch_no', ''),
            'merchantNo'       => self::$config->get('mch_id', ''),
            'mobile'           => $requestParams['mobile'] ?? '',
            'userID'           => $requestParams['user_id'] ?? '',
            'lon'              => $requestParams['lon'] ?? '',
            'lat'              => $requestParams['lat'] ?? '',
            'riskLevel'        => $requestParams['risk_level'] ?? '',
            'noticeUrl'        => self::$config->get('sign_notify_url', ''),
            'noticePara'       => $requestParams['return_param'] ?? '',
            'returnUrl'        => self::$config->get('sign_return_url', ''),
        ];

        return $params;
    }
}
