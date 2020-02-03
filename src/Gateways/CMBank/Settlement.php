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
 * @date    : 2019/11/27 7:03 PM
 * @version : 1.0.0
 * @desc    : 按商户or银行订单日期查询已结账订单: 按商户订单日期查询批量订单明细。注意：查询结果不包含退款信息。
 **/
class Settlement extends CMBaseObject implements IGatewayRequest
{
    // 按商户时间查询
    const MCH_METHOD = 'NetPayment/BaseHttp.dll?QuerySettledOrderByMerchantDate';

    const SANDBOX_MCH_METHOD = 'NetPayment_dl/BaseHttp.dll?QuerySettledOrderByMerchantDate';


    // 按银行时间查询
    const BANK_METHOD = 'NetPayment/BaseHttp.dll?QuerySettledOrderByBankDate';

    const SANDBOX_BANK_METHOD = 'NetPayment_dl/BaseHttp.dll?QuerySettledOrderByBankDate';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        $this->gatewayUrl = 'https://payment.ebank.cmbchina.com/%s';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'http://121.15.180.66:801/%s';
        }
        if (isset($requestParams['mode']) && $requestParams['mode'] === 'bank') {
            $method = self::BANK_METHOD;
            if ($this->isSandbox) {
                $method = self::SANDBOX_BANK_METHOD;
            }
        } else {
            $method = self::MCH_METHOD;
            if ($this->isSandbox) {
                $method = self::SANDBOX_MCH_METHOD;
            }
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
            'beginDate'    => $startTime,
            'endDate'      => $endTime,
            'operatorNo'   => $requestParams['operator_id'] ?? '',
            'nextKeyValue' => $requestParams['next_key_value'] ?? '', // 首次查询填“空”; 后续查询，按应答报文中返回的nextKeyValue值原样传入.
        ];

        return $params;
    }
}
