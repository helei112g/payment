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
use Payment\Contracts\ITransferProxy;
use Payment\Exceptions\GatewayException;
use Payment\Gateways\Alipay\Bill;
use Payment\Gateways\Alipay\CancelTrade;
use Payment\Gateways\Alipay\CloseTrade;
use Payment\Gateways\Alipay\Notify;
use Payment\Gateways\Alipay\Refund;
use Payment\Gateways\Alipay\RefundQuery;
use Payment\Gateways\Alipay\TradeQuery;
use Payment\Gateways\Alipay\Transfer;
use Payment\Gateways\Alipay\TransferQuery;
use Payment\Payment;
use Payment\Supports\BaseObject;

/**
 * @package Payment\Proxys
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/28 10:25 PM
 * @version : 1.0.0
 * @desc    : 支付宝的代理类
 **/
class AlipayProxy extends BaseObject implements IPayProxy, IQueryProxy, ITransferProxy
{
    /**
     * 支付操作
     * @param string $channel
     * @param array $requestParams
     * @return mixed
     * @throws InvalidArgumentException
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
        return "Payment\\Gateways\\Alipay\\{$name}Charge";
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
            $obj = new Refund();
            return $obj->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 同步、异步通知
     * @param IPayNotify $callback
     * @return mixed
     * @throws GatewayException
     */
    public function notify(IPayNotify $callback)
    {
        try {
            $n    = new Notify();
            $data = $n->request(); // 获取数据
        } catch (GatewayException $e) {
            throw $e;
        }

        // 异步 async，同步 sync
        $flag = $callback->handle('Alipay', $data['notify_type'], $data['notify_way'], $data['notify_data']);

        return $n->response($flag);
    }

    /**
     * 取消交易
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function cancel(array $requestParams)
    {
        try {
            $obj = new CancelTrade();
            return $obj->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 关闭交易
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function close(array $requestParams)
    {
        try {
            $obj = new CloseTrade();
            return $obj->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
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
            $obj = new TradeQuery();
            return $obj->request($requestParams);
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
            $obj = new RefundQuery();
            return $obj->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
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
            $obj = new Bill();
            return $obj->request($requestParams);
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
        throw new GatewayException('ali not support the method.', Payment::NOT_SUPPORT_METHOD);
    }

    /**
     * 支付宝到支付宝转账
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function transfer(array $requestParams)
    {
        try {
            $obj = new Transfer();
            return $obj->request($requestParams);
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
        try {
            $obj = new TransferQuery();
            return $obj->request($requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }
}
