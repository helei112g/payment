<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:20
 * @description: 支付宝移动支付接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Ali;


use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\AppChargeData;
use Payment\Common\AliConfig;
use Payment\Config;
use Payment\Utils\ArrayUtil;
use Payment\Utils\StrUtil;

class AliAppCharge extends AliBaseStrategy
{
    /**
     * 获取支付对应的数据完成类
     * @return string
     * @author helei
     */
    protected function getBuildDataClass()
    {
        $this->config->method = AliConfig::ALI_TRADE_APP;
        // 以下两种方式任选一种
        return AppChargeData::class;

        //return 'Payment\Common\Ali\Data\Charge\AppChargeData';
    }

    /**
     * 返回app 支付数据  sign  在签名内部，已经进行 base64_encode 了
     * @param array $data
     * @return string
     */
    protected function retData(array $data)
    {
        $data = ArrayUtil::arraySort($data);// 因为签名时进行了排序，此处返回时也要进行排序，否则支付验证签名无法通过

        $sign = $data['sign'];
        unset($data['sign']);
        if ($this->config->version === Config::ALI_API_VERSION) {
            // 支付宝新版本
            foreach ($data as &$value) {
                $value = StrUtil::characet($value, $this->config->inputCharset);
            }

            $data['sign'] = $sign;// sign  需要放在末尾
            return http_build_query($data);
        }

        // 如果是移动支付，直接返回数据信息。并且对sign做urlencode编码
        $data['sign'] = '"' . urlencode($sign) . '"';

        return ArrayUtil::createLinkstring($data);
    }
}