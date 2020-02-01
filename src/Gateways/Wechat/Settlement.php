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
use Payment\Payment;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/4/1 8:28 PM
 * @version : 1.0.0
 * @desc    : 下载资金账单
 **/
class Settlement extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'pay/downloadfundflow';

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
            $data = $this->requestWXApi(self::METHOD, $requestParams);

            return $this->formatBill($data);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * @param string $data
     * @return array
     */
    protected function formatBill(string $data)
    {
        $fields = [
            'book_time', 'trade_no', 'amount_bill_bo', 'business_name', 'business_type',
            'income_type', 'income_amount', 'account_balance', 'operator', 'mark', 'business_no',
        ];
        $result = [];
        $tmpArr = explode(PHP_EOL, $data);
        foreach ($tmpArr as $index => $item) {
            if ($index === 0) {
                continue;
            }
            $tmpItem   = explode(',', $item);
            $tmpResult = [];
            foreach ($fields as $field) {
                $value             = current($tmpItem);
                $tmpResult[$field] = trim($value, '`');
                next($tmpItem);
            }
            if (empty($tmpResult)) {
                continue;
            }

            unset($tmpItem);
            $result[] = $tmpResult;
        }
        return $result;
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getSelfParams(array $requestParams)
    {
        $selfParams = [
            'bill_date'    => $requestParams['bill_date'] ?? '',
            'account_type' => $requestParams['bill_type'] ?? 'Operation',
            //'tar_type'     => $requestParams['tar_type'] ?? '',
        ];

        return $selfParams;
    }
}
