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
 * @date    : 2019/11/27 7:43 PM
 * @version : 1.0.0
 * @desc    : 查询退款: 该接口可选对接。商户也可登录“网上商户结账处理系统”查询退款信息。
 **/
class RefundQuery extends CMBaseObject implements IGatewayRequest
{
    const ONLINE_METHOD = 'https://payment.ebank.cmbchina.com/NetPayment/BaseHttp.dll?QuerySettledRefund';

    const SANDBOX_METHOD = 'http://121.15.180.66:801/netpayment_dl/BaseHttp.dll?QuerySettledRefund';

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
        $orderDate = $requestParams['order_date'] ?? strtotime('-1 days');
        $orderDate = date('Ymd', $orderDate);

        $params = [
            'dateTime'         => date('YmdHis', $nowTime),
            'branchNo'         => self::$config->get('branch_no', ''),
            'merchantNo'       => self::$config->get('mch_id', ''),
            'type'             => $requestParams['type'] ?? 'A',
            'orderNo'          => $requestParams['order_no'] ?? '',
            'date'             => $orderDate,
            'merchantSerialNo' => $requestParams['merchant_serial_no'] ?? '',
            'bankSerialNo'     => $requestParams['bank_serial_no'] ?? '',
        ];

        return $params;
    }
}
