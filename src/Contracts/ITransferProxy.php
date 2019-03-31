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
 * @date    : 2019/3/28 10:32 PM
 * @version : 1.0.0
 * @desc    :
 **/
interface ITransferProxy
{

    /**
     * 支付宝到支付宝转账
     * @param array $requestParams
     * @return mixed
     */
    public function transfer(array $requestParams);
}
