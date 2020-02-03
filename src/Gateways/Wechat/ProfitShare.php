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
 * @date    : 2019/11/26 8:52 PM
 * @version : 1.0.0
 * @desc    : 请求单次分账
 **/
class ProfitShare extends WechatBaseObject implements IGatewayRequest
{
    // 单词分账
    const METHOD_SIGN = 'secapi/pay/profitsharing';

    // 多次分账
    const METHOD_MULTI = 'secapi/pay/multiprofitsharing';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        $this->setSignType(self::SIGN_TYPE_SHA);
        try {
            $url = self::METHOD_SIGN;
            if (isset($requestParams['mode']) && $requestParams['mode'] === 'multi') {
                $url = self::METHOD_MULTI;
            }

            return $this->requestWXApi($url, $requestParams);
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
        $receivers = $requestParams['receivers'] ?? '';
        if ($receivers) {
            $receivers = json_encode($receivers);
        } else {
            $receivers = '';
        }

        $selfParams = [
            'transaction_id' => $requestParams['transaction_id'] ?? '',
            'out_trade_no'   => $requestParams['out_trade_no'] ?? '',
            'receivers'      => $requestParams['receivers'] ?? '',
        ];

        return $selfParams;
    }
}
