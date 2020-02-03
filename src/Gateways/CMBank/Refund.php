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
 * @date    : 2019/11/27 7:23 PM
 * @version : 1.0.0
 * @desc    : 退款: 该接口可选对接。商户也可登录“网上商户结账处理系统”执行退款。
 **/
class Refund extends CMBaseObject implements IGatewayRequest
{
    const METHOD = 'NetPayment/BaseHttp.dll?DoRefundV2';

    const SANDBOX_METHOD = 'NetPayment_dl/BaseHttp.dll?DoRefundV2';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        $method           = self::METHOD;
        $this->gatewayUrl = 'https://payment.ebank.cmbchina.com/%s';
        if ($this->isSandbox) {
            $method           = self::SANDBOX_METHOD;
            $this->gatewayUrl = 'http://121.15.180.66:801/%s';
        }
        try {
            return $this->requestCMBApi($method, $requestParams);
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
        $nowTime = time();

        $params = [
            'dateTime'       => date('YmdHis', $nowTime),
            'branchNo'       => self::$config->get('branch_no', ''),
            'merchantNo'     => self::$config->get('mch_id', ''),
            'date'           => date('Ymd', $requestParams['date'] ?? $nowTime), // 商户订单日期,格式：yyyyMMdd
            'orderNo'        => $requestParams['trade_no'] ?? '',
            'refundSerialNo' => $requestParams['refund_no'] ?? '',
            'amount'         => $requestParams['refund_fee'] ?? '',
            'desc'           => $requestParams['reason'] ?? '',
            'operatorNo'     => $requestParams['operator_id'] ?? '',
            //'encrypType'   => $requestParams['encryp_type'] ?? '', // 暂时只支持不加密，后续实现一下
            //'pwd' => $requestParams['pwd'] ?? '',
            //'refundMode' => $requestParams['refund_mode'] ?? '',// 退款标识字段空/“A”
        ];

        return $params;
    }
}
