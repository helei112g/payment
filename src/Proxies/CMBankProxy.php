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

use Payment\Contracts\IPayNotify;
use Payment\Contracts\IPayProxy;
use Payment\Contracts\IQueryProxy;
use Payment\Exceptions\GatewayException;
use Payment\Gateways\CMBank\PublicKeyQuery;
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
     */
    public function pay(string $channel, array $requestParams)
    {
        // TODO: Implement pay() method.
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
     */
    public function cancel(array $requestParams)
    {
        // TODO: Implement cancel() method.
    }

    /**
     * 关闭交易
     * @param array $requestParams
     * @return mixed
     */
    public function close(array $requestParams)
    {
        // TODO: Implement close() method.
    }

    /**
     * 交易查询
     * @param array $requestParams
     * @return mixed
     */
    public function tradeQuery(array $requestParams)
    {
        // TODO: Implement tradeQuery() method.
    }

    /**
     * 退款查询
     * @param array $requestParams
     * @return mixed
     */
    public function refundQuery(array $requestParams)
    {
        // TODO: Implement refundQuery() method.
    }

    /**
     * 转账查询
     * @param array $requestParams
     * @return mixed
     */
    public function transferQuery(array $requestParams)
    {
        // TODO: Implement transferQuery() method.
    }

    /**
     * 账单查询
     * @param array $requestParams
     * @return mixed
     */
    public function billDownload(array $requestParams)
    {
        // TODO: Implement billDownload() method.
    }

    /**
     * 打款结算查询
     * @param array $requestParams
     * @return mixed
     */
    public function settleDownload(array $requestParams)
    {
        // TODO: Implement settleDownload() method.
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
