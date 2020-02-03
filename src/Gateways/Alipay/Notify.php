<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\Alipay;

use Payment\Exceptions\GatewayException;
use Payment\Payment;

/**
 * @package Payment\Gateways\Alipay
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2020/2/2 9:56 下午
 * @version : 1.0.0
 * @desc    : 处理notify的问题
 **/
class Notify extends AliBaseObject
{
    /**
     * 获取请求数据
     * @throws GatewayException
     */
    public function request()
    {
        $resArr = $this->getNotifyData();
        if (empty($resArr)) {
            throw new GatewayException('the notify data is empty', Payment::NOTIFY_DATA_EMPTY);
        }

        if (isset($resArr['notify_type']) && isset($resArr['trade_status'])) {
            $notifyWay = 'async'; // 异步
        } else {
            $notifyWay = 'sync'; // 同步
        }

        $sign     = $resArr['sign'];
        $signType = $resArr['sign_type'];
        unset($resArr['sign'], $resArr['sign_type']);

        if ($this->verifySignForASync($resArr, $sign, $signType) === false) {
            throw new GatewayException('check notify data sign failed', Payment::SIGN_ERR, $resArr);
        }

        if (!isset($resArr['app_id']) || $resArr['app_id'] != self::$config->get('app_id', '')) {
            throw new GatewayException('mch info is error', Payment::MCH_INFO_ERR, $resArr);
        }

        return [
            'notify_type' => 'pay',
            'notify_way'  => $notifyWay,
            'notify_data' => $resArr,
        ];
    }

    /**
     * 获取异步通知数据
     * @return array
     */
    protected function getNotifyData()
    {
        $data = empty($_POST) ? $_GET : $_POST;
        if (empty($data) || !is_array($data)) {
            return [];
        }

        return $data;
    }

    /**
     * 响应数据
     * @param bool $flag
     * @return string
     */
    public function response(bool $flag)
    {
        if ($flag) {
            return 'success';
        }
        return 'fail';
    }

    /**
     * notify 不需要该方法，不实现
     * @param array $requestParams
     * @return mixed
     */
    protected function getBizContent(array $requestParams)
    {
        return [];
    }
}
