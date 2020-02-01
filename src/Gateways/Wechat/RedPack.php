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
 * @date    : 2019/4/1 8:30 PM
 * @version : 1.0.0
 * @desc    : 发送红包
 **/
class RedPack extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'mmpaymkttransfers/sendredpack';

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
        $totalFee = bcmul($requestParams['amount'], 100, 0);

        $selfParams = [
            'mch_billno'   => $requestParams['bill_no'] ?? '',
            'send_name'    => $requestParams['send_name'] ?? '',
            're_openid'    => $requestParams['re_openid'] ?? '', // 接受红包的用户openid
            'total_amount' => $totalFee,
            'total_num'    => $requestParams['total_num'] ?? '',
            'wishing'      => $requestParams['wishing'] ?? '',
            'client_ip'    => $requestParams['client_ip'] ?? '',
            'act_name'     => $requestParams['act_name'] ?? '',
            'remark'       => $requestParams['remark'] ?? '',
            'scene_id'     => $requestParams['scene_id'] ?? '', // 发放红包使用场景，红包金额大于200或者小于1元时必传
            'risk_info'    => $requestParams['risk_info'] ?? '',
        ];

        return $selfParams;
    }
}
