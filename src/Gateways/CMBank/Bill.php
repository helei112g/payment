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
 * @desc    : 查询入账明细: 查询商户入账明细，商户系统应以招行入账明细为准进行对账，对账不平的交易进行退款或请款协商。
 **/
class Bill extends CMBaseObject implements IGatewayRequest
{
    const ONLINE_METHOD = 'https://payment.ebank.cmbchina.com/NetPayment/BaseHttp.dll?QueryAccountList';

    const SANDBOX_METHOD = 'http://121.15.180.66:801/NetPayment_dl/BaseHttp.dll?QueryAccountList';

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
        $nowTime  = time();
        $billData = $requestParams['bill_date'] ?? strtotime('-1 days');
        $billData = date('Ymd', $billData);

        $params = [
            'dateTime'     => date('YmdHis', $nowTime),
            'branchNo'     => self::$config->get('branch_no', ''),
            'merchantNo'   => self::$config->get('mch_id', ''),
            'date'         => $billData,
            'operatorNo'   => $requestParams['operator_id'] ?? '',
            'nextKeyValue' => $requestParams['next_key_value'] ?? '', // 首次查询填“空”; 后续查询，按应答报文中返回的nextKeyValue值原样传入.
        ];

        return $params;
    }
}
