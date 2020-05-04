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
use Payment\Helpers\ArrayUtil;
use Payment\Helpers\StrUtil;
use Payment\Payment;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/4/1 8:24 PM
 * @version : 1.0.0
 * @desc    : app支付
 **/
class AppCharge extends WechatBaseObject implements IGatewayRequest
{
    const METHOD = 'pay/unifiedorder';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        try {
            $ret = $this->requestWXApi(self::METHOD, $requestParams);
        } catch (GatewayException $e) {
            throw $e;
        }

        // 生成app端需要的数据
        if (is_array($ret) && $ret['return_code'] === 'SUCCESS' && $ret['result_code'] === 'SUCCESS') {
            $payData = [
                'appid'     => $ret['appid'],
                'partnerid' => $ret['mch_id'],
                'prepayid'  => $ret['prepay_id'],
                'package'   => 'Sign=WXPay', // 微信要求固定值
                'noncestr'  => StrUtil::getNonceStr(self::NONCE_LEN),
                'timestamp' => time(),
            ];

            // 添加签名
            $payData = ArrayUtil::paraFilter($payData);
            $payData = ArrayUtil::arraySort($payData);

            try {
                $signStr         = ArrayUtil::createLinkstring($payData);
                $payData['sign'] = $this->makeSign($signStr);
            } catch (\Exception $e) {
                throw new GatewayException($e->getMessage(), Payment::PARAMS_ERR);
            }

            // 这三个字段是为了让前端的判断保持一致
            $payData['return_code'] = 'SUCCESS';
            $payData['return_msg']  = $ret['return_msg'];
            $payData['result_code'] = 'SUCCESS';

            $ret = $payData;
        }

        return $ret;
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getSelfParams(array $requestParams)
    {
        $limitPay = self::$config->get('limit_pay', '');
        if ($limitPay) {
            $limitPay = $limitPay[0];
        } else {
            $limitPay = '';
        }
        $nowTime    = time();
        $timeExpire = intval($requestParams['time_expire']);
        if (!empty($timeExpire)) {
            $timeExpire = date('YmdHis', $timeExpire);
        } else {
            $timeExpire = date('YmdHis', $nowTime + 1800); // 默认半小时过期
        }

        $receipt   = $requestParams['receipt'] ?? false;
        $totalFee  = bcmul($requestParams['amount'], 100, 0);
        $sceneInfo = $requestParams['scene_info'] ?? '';
        if ($sceneInfo) {
            $sceneInfo = json_encode(['store_info' => $sceneInfo]);
        } else {
            $sceneInfo = '';
        }

        $selfParams = [
            'device_info'      => $requestParams['device_info'] ?? '',
            'body'             => $requestParams['subject'] ?? '',
            'detail'           => $requestParams['body'] ?? '',
            'attach'           => $requestParams['return_param'] ?? '',
            'out_trade_no'     => $requestParams['trade_no'] ?? '',
            'fee_type'         => self::$config->get('fee_type', 'CNY'),
            'total_fee'        => $totalFee,
            'spbill_create_ip' => $requestParams['client_ip'] ?? '',
            'time_start'       => date('YmdHis', $nowTime),
            'time_expire'      => $timeExpire,
            'goods_tag'        => $requestParams['goods_tag'] ?? '',
            'notify_url'       => self::$config->get('notify_url', ''),
            'trade_type'       => 'APP',
            'limit_pay'        => $limitPay,
            'receipt'          => $receipt === true ? 'Y' : '',
            'scene_info'       => $requestParams['scene_info'] ?? '', // 场景信息
        ];

        return $selfParams;
    }
}
