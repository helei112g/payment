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
 * @desc    : 查询接口
 **/
interface IQueryProxy
{
    /**
     * 交易查询
     * @param array $requestParams
     * @return mixed
     */
    public function tradeQuery(array $requestParams);

    /**
     * 退款查询
     * @param array $requestParams
     * @return mixed
     */
    public function refundQuery(array $requestParams);

    /**
     * 转账查询
     * @param array $requestParams
     * @return mixed
     */
    public function transferQuery(array $requestParams);

    /**
     * 账单查询
     * @param array $requestParams
     * @return mixed
     */
    public function billDownload(array $requestParams);

    /**
     * 打款结算查询
     * @param array $requestParams
     * @return mixed
     */
    public function settleDownload(array $requestParams);
}
