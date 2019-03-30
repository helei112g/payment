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
use Payment\Contracts\IPayProxy;
use Payment\Exceptions\GatewayException;
use Payment\Payment;
use Payment\Supports\BaseObject;

/**
 * @package Payment\Proxys
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/28 10:25 PM
 * @version : 1.0.0
 * @desc    :
 **/
class AlipayProxy extends BaseObject implements IPayProxy
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
    protected function getChargeClass(string $channel)
    {
        $name = ucfirst(str_replace(['-', '_', ''], '', $channel));
        return "Payment\\Gateways\\Alipay\\{$name}Charge";
    }

    /**
     * 退款操作
     * @param array $requestParams
     * @return mixed
     */
    public function refund(array $requestParams)
    {
        // TODO: Implement refund() method.
    }

    /**
     * 同步通知
     * @return mixed
     */
    public function callback()
    {
        // TODO: Implement callback() method.
    }

    /**
     * 异步通知
     * @return mixed
     */
    public function notify()
    {
        // TODO: Implement notify() method.
    }

    /**
     * 异步通知的返回
     * @param bool $flag
     * @return mixed
     */
    public function notifyRely(bool $flag)
    {
        // TODO: Implement notifyRely() method.
    }
}
