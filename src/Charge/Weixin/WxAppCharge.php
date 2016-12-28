<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:56
 * @description: 微信 app 支付接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Weixin;

use Payment\Common\Weixin\Data\BackAppChargeData;
use Payment\Common\Weixin\Data\Charge\AppChargeData;
use Payment\Common\Weixin\WxBaseStrategy;

class WxAppCharge extends WxBaseStrategy
{

    protected function getBuildDataClass()
    {
        return AppChargeData::class;
    }

    /**
     * 处理APP支付的返回值。直接返回与微信文档对应的字段
     * @param array $ret
     *
     * @return array $data
     *
     * ```php
     * $data = [
     *  'appid' => '',   // 应用ID
     *  'partnerid' => '',   // 商户号
     *  'prepayid'  => '',   // 预支付交易会话ID
     *  'package'   => '',  // 扩展字段  固定值：Sign=WXPay
     *  'noncestr'  => '',   // 随机字符串
     *  'timestamp' => '',   // 时间戳
     *  'sign'  => '',  // 签名
     * ];
     * ```
     * @author helei
     */
    protected function retData(array $ret)
    {
        $back = new BackAppChargeData($this->config, $ret);

        $back->setSign();
        $backData = $back->getData();

        return $backData;
    }
}