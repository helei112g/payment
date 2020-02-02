<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Contracts;

/**
 * @package Payment\Contracts
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/28 10:27 PM
 * @version : 1.0.0
 * @desc    : 支付接口
 **/
interface IPayProxy
{
    /**
     * 支付操作
     * @param string $channel
     * @param array $requestParams
     * @return mixed
     */
    public function pay(string $channel, array $requestParams);

    /**
     * 退款操作
     * @param array $requestParams
     * @return mixed
     */
    public function refund(array $requestParams);


    /**
     * 异步通知
     * @param IPayNotify $callback
     * @return mixed
     */
    public function notify(IPayNotify $callback);

    /**
     * 取消交易
     * @param array $requestParams
     * @return mixed
     */
    public function cancel(array $requestParams);

    /**
     * 关闭交易
     * @param array $requestParams
     * @return mixed
     */
    public function close(array $requestParams);
}
