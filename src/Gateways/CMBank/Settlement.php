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
 * @desc    : 按商户订单日期查询已结账订单: 按商户订单日期查询批量订单明细。注意：查询结果不包含退款信息。
 **/
class Settlement extends CMBaseObject implements IGatewayRequest
{
    const ONLINE_METHOD = 'https://payment.ebank.cmbchina.com/NetPayment/BaseHttp.dll?QuerySettledOrderByMerchantDate';

    const SANDBOX_METHOD = 'http://121.15.180.66:801/NetPayment_dl/BaseHttp.dll?QuerySettledOrderByMerchantDate';

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
        $nowTime   = time();
        $startTime = $requestParams['start_time'] ?? strtotime('-1 days');
        $startTime = date('Ymd', $startTime);

        $endTime = $requestParams['start_time'] ?? $nowTime;
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
