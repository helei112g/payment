<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\Wechat;

use Payment\Contracts\IGatewayRequest;
use Payment\Exceptions\GatewayException;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/4/7 9:39 AM
 * @version : 1.0.0
 * @desc    : 拉取订单评价数据
 **/
class CommentQuery extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'billcommentsp/batchquerycomment';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        try {
            return $this->requestWXApi(self::METHOD, $requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getSelfParams(array $requestParams)
    {
        $selfParams = [
            'begin_time' => $requestParams['begin_time'] ?? date('YmdHis', strtotime('-1days')),
            'end_time'   => $requestParams['end_time'] ?? date('YmdHis', time()),
            'offset'     => $requestParams['offset'] ?? 0,
            'limit'      => $requestParams['limit'] ?? 200,
        ];

        return $selfParams;
    }
}
