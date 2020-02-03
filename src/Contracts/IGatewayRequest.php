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

use Payment\Exceptions\GatewayException;

/**
 * @package Payment\Contracts
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/30 10:29 AM
 * @version : 1.0.0
 * @desc    : 网关功能标准接口
 **/
interface IGatewayRequest
{
    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams);
}
