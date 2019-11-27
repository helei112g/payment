<?php

namespace Payment\Gateways\CMBank;


use Payment\Contracts\IGatewayRequest;

/**
 * @package Payment\Gateways\CMBank
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/11/27 7:47 PM
 * @version : 1.0.0
 * @desc    : 取消支付协议: 商户如需提供取消客户协议功能，可对接该接口
 **/
class ProtocolCancel extends CMBaseObject implements IGatewayRequest
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

        $params = [
            'dateTime'         => date('YmdHis', $nowTime),
            'txCode'           => 'CMQX',
            'branchNo'         => self::$config->get('branch_no', ''),
            'merchantNo'       => self::$config->get('mch_id', ''),
            'merchantSerialNo' => $requestParams['merchant_serial_no'] ?? '',
            'agrNo'            => $requestParams['agr_no'] ?? '',
        ];

        return $params;
    }
}
