<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Proxies;

use InvalidArgumentException;
use Payment\Contracts\IGatewayRequest;
use Payment\Contracts\IPayNotify;
use Payment\Contracts\IPayProxy;
use Payment\Contracts\IQueryProxy;
use Payment\Exceptions\GatewayException;
use Payment\Gateways\CMBank\Bill;
use Payment\Gateways\CMBank\BillRefund;
use Payment\Gateways\CMBank\PublicKeyQuery;
use Payment\Gateways\CMBank\Refund;
use Payment\Gateways\CMBank\RefundQuery;
use Payment\Gateways\CMBank\Settlement;
use Payment\Gateways\CMBank\TradeQuery;
use Payment\Payment;
use Payment\Supports\BaseObject;

/**
 * @package Payment\Proxys
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/28 10:25 PM
 * @version : 1.0.0
 * @desc    : http://121.15.180.72/OpenAPI2/DOC/DOCdefault.aspx
 **/
class CMBankProxy extends BaseObject implements IPayProxy, IQueryProxy
{
    /**
     * 支付操作
     * @param string $channel
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function pay(string $channel, array $requestParams)
    {
        $className = $this->getChargeClass($channel);
        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Gateway [%s] not exists.', $className), Payment::CLASS_NOT_EXIST);
        }

        try {
            /**
             * @var IGatewayRequest $charge
             */
            $charge = new $className();
            return $charge->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 获取支付类
     * @param string $channel
     * @return string
     */
    private function getChargeClass(string $channel)
    {
        $name = ucfirst(str_replace(['-', '_', ''], '', $channel));
        return "Payment\\Gateways\\CMBank\\{$name}Charge";
    }

    /**
     * 退款操作
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function refund(array $requestParams)
    {
        try {
            $trade = new Refund();
            return $trade->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 异步通知
     * @param IPayNotify $callback
     * @return mixed
     */
    public function notify(IPayNotify $callback)
    {
        // TODO: Implement notify() method.
    }

    /**
     * 取消交易
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function cancel(array $requestParams)
    {
        throw new GatewayException('cmb not support the method.', Payment::NOT_SUPPORT_METHOD);
    }

    /**
     * 关闭交易
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function close(array $requestParams)
    {
        throw new GatewayException('cmb not support the method.', Payment::NOT_SUPPORT_METHOD);
    }

    /**
     * 交易查询
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function tradeQuery(array $requestParams)
    {
        try {
            $trade = new TradeQuery();
            return $trade->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 退款查询
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function refundQuery(array $requestParams)
    {
        try {
            $trade = new RefundQuery();
            return $trade->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 转账查询
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function transferQuery(array $requestParams)
    {
        throw new GatewayException('cmb not support the method.', Payment::NOT_SUPPORT_METHOD);
    }

    /**
     * 账单查询
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function billDownload(array $requestParams)
    {
        try {
            if ($requestParams['type'] === 'refund') {
                $i = new BillRefund(); // 查询退款账单
            } else {
                $i = new Bill(); // 查询交易账单
            }

            return $i->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 打款结算查询
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function settleDownload(array $requestParams)
    {
        try {
            $trade = new Settlement();
            return $trade->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 获取公钥
     * @throws GatewayException
     */
    public function getPubKey()
    {
        try {
            $i = new PublicKeyQuery();
            $i->request([]);
        } catch (GatewayException $e) {
            throw $e;
        }
    }
}
