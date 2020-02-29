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

use Payment\Exceptions\GatewayException;
use Payment\Helpers\ArrayUtil;
use Payment\Helpers\DataParser;
use Payment\Payment;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2020/2/1 3:19 下午
 * @version : 1.0.0
 * @desc    : 异步通知数据处理
 **/
class Notify extends WechatBaseObject
{
    /**
     * @throws GatewayException
     */
    public function request()
    {
        $resArr = $this->getNotifyData();
        if (empty($resArr)) {
            throw new GatewayException('the notify data is empty', Payment::NOTIFY_DATA_EMPTY);
        }

        if (!is_array($resArr) || $resArr['return_code'] !== self::REQ_SUC) {
            throw new GatewayException($this->getErrorMsg($resArr), Payment::GATEWAY_REFUSE, $resArr);
        } elseif (isset($resArr['result_code']) && $resArr['result_code'] !== self::REQ_SUC) {
            throw new GatewayException(sprintf('code:%d, desc:%s', $resArr['err_code'], $resArr['err_code_des']), Payment::GATEWAY_CHECK_FAILED, $resArr);
        }

        if (isset($resArr['sign']) && $this->verifySign($resArr) === false) {
            throw new GatewayException('check notify data sign failed', Payment::SIGN_ERR, $resArr);
        }

        // 检查商户是否正确
        if (self::$config->get('app_id', '') !== $resArr['appid'] || self::$config->get('mch_id', '') !== $resArr['mch_id']) {
            throw new GatewayException('mch info is error', Payment::MCH_INFO_ERR, $resArr);
        }

        $notifyType = 'pay';
        // 支持微信退款回调  req_info（解密）
        if (isset($resArr['req_info'])) {
            $notifyType = 'refund';
            // 解密返回信息
            $decryptReqInfo = base64_decode($resArr['req_info']);
            $decryptReqInfo = openssl_decrypt($decryptReqInfo, 'aes-256-ecb', md5($this->merKey), OPENSSL_RAW_DATA);
            $resArr         = DataParser::toArray($decryptReqInfo);
        }

        return [
            'notify_type' => $notifyType,
            'notify_data' => $resArr
        ];
    }

    protected function getNotifyData()
    {
        // php://input 带来的内存压力更小
        $data = @file_get_contents('php://input');// 等同于微信提供的：$GLOBALS['HTTP_RAW_POST_DATA']
        // 支付成功异步通知测试数据
/*        $data = '
<xml>
  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
  <attach><![CDATA[支付测试]]></attach>
  <bank_type><![CDATA[CFT]]></bank_type>
  <fee_type><![CDATA[CNY]]></fee_type>
  <is_subscribe><![CDATA[Y]]></is_subscribe>
  <mch_id><![CDATA[10000100]]></mch_id>
  <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
  <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
  <out_trade_no><![CDATA[1409811653]]></out_trade_no>
  <result_code><![CDATA[SUCCESS]]></result_code>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
  <time_end><![CDATA[20140903131540]]></time_end>
  <total_fee>1</total_fee>
<coupon_fee><![CDATA[10]]></coupon_fee>
<coupon_count><![CDATA[1]]></coupon_count>
<coupon_type><![CDATA[CASH]]></coupon_type>
<coupon_id><![CDATA[10000]]></coupon_id>
<coupon_fee><![CDATA[100]]></coupon_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
</xml>
        ';*/

        // 退款异步通知
        /*        $data = '
        <xml>
        <return_code>SUCCESS</return_code>
           <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
           <mch_id><![CDATA[10000100]]></mch_id>
           <nonce_str><![CDATA[TeqClE3i0mvn3DrK]]></nonce_str>
           <req_info><![CDATA[T87GAHG17TGAHG1TGHAHAHA1Y1CIOA9UGJH1GAHV871HAGAGQYQQPOOJMXNBCXBVNMNMAJAA]]></req_info>
        </xml>
                ';*/
        // 将xml数据格式化为数组
        $arrData = DataParser::toArray($data);
        if (empty($arrData)) {
            return [];
        }

        // 移除值中的空格  xml转化为数组时，CDATA 数据会被带入额外的空格。
        $arrData = ArrayUtil::paraFilter($arrData);
        return $arrData;
    }

    /**
     * 向微信响应处理结果
     * @param bool $flag
     * @return bool|string
     */
    public function response(bool $flag)
    {
        // 默认为成功
        $result = [
            'return_code' => 'SUCCESS',
            'return_msg'  => 'OK',
        ];
        if (!$flag) {
            // 失败
            $result = [
                'return_code' => 'FAIL',
                'return_msg'  => 'mch have error',
            ];
        }

        return DataParser::toXml($result);
    }

    /**
     * 改方法在这里不做处理
     * @param array $requestParams
     * @return mixed
     */
    protected function getSelfParams(array $requestParams)
    {
        return [];
    }
}
