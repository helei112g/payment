<?php
/**
 * Created by PhpStorm.
 * User: helei  <dayugog@gmail.com>
 * Date: 2016/12/28
 * Time: 20:24
 */

namespace Payment\Common\Ali\Data\Charge;


use Payment\Common\PayException;

/**
 * 支付宝 扫码支付
 * Class QrChargeData
 * @package Payment\Common\Ali\Data\Charge
 */
class QrChargeData extends ChargeBaseData
{

    protected function alipay1_0Data($timeExpire = '')
    {
        throw new PayException('支付宝老版本，不支持扫码支付！');
    }

    /**
     * 生成相关支付数据
     * @param string $timeExpire
     *
     * @return array
     */
    protected function alipay2_0Data($timeExpire = '')
    {
        $signData = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'charset'       => $this->inputCharset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,
            'notify_url'    => $this->notifyUrl,

            // 业务参数  新版支付宝，将所有业务参数设置到改字段中了，  这样不错
            'biz_content'   => $this->getBizContent($timeExpire),
        ];

        return $signData;
    }

    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @param string $timeExpire 订单过期时间，  单位 分钟
     *
     * @return string
     */
    private function getBizContent($timeExpire = '')
    {
        $content = [
            'body'          => strval($this->body),
            'subject'       => strval($this->subject),
            'out_trade_no'  => strval($this->order_no),
            'total_amount'  => strval($this->amount),
        ];

        if (! empty($timeExpire)) {
            $content['timeout_express'] = $this->timeExpire . 'm';// 超时时间 统一使用分钟计算
        }

        $partner = $this->partner;
        if (! empty($partner)) {
            $content['seller_id'] = strval($partner);
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}