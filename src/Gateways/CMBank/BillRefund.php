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
 * @date    : 2019/11/27 7:32 PM
 * @version : 1.0.0
 * @desc    : 按照退款日期查询退款的账单数据，招商的支付账单与退款账单是分开的，对账需要拉两部分数据
 **/
class BillRefund extends CMBaseObject implements IGatewayRequest
{
    const METHOD = 'NetPayment/BaseHttp.dll?QueryRefundByDateV2';

    const SANDBOX_METHOD = 'Netpayment_dl/BaseHttp.dll?QueryRefundByDateV2';

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
        $nowTime   = time();
        $startTime = $requestParams['start_time'] ?? strtotime('-1 days');
        $startTime = date('Ymd', $startTime);

        $endTime = $requestParams['end_time'] ?? $nowTime;
        $endTime = date('Ymd', $endTime);

        $params = [
            'dateTime'     => date('YmdHis', $nowTime),
            'branchNo'     => self::$config->get('branch_no', ''),
            'merchantNo'   => self::$config->get('mch_id', ''),
            'beginDate'    => $startTime, // 开始日期,退款日期格式：yyyyMMdd
            'endDate'      => $endTime, // 结束日期,格式：yyyyMMdd
            'operatorNo'   => $requestParams['operator_id'] ?? '',
            'nextKeyValue' => $requestParams['next_key_value'] ?? '',
        ];

        return $params;
    }
}
