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
use Payment\Utils\ArrayUtil;
use Payment\Utils\StrUtil;

class AliAppCharge extends AliBaseStrategy
{
    /**
     * 获取支付对应的数据完成类
     * @return string
     * @author helei
     */
    public function getBuildDataClass()
    {
        $this->config->method = AliConfig::APP_PAY_METHOD;
        // 以下两种方式任选一种
        return AppChargeData::class;
    }

    /**
     * 返回app 支付数据  sign  在签名内部，已经进行 base64_encode 了
     * @param array $data
     * @return string
     */
    protected function retData(array $data)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        $data = ArrayUtil::arraySort($data);// 因为签名时进行了排序，此处返回时也要进行排序，否则支付验证签名无法通过

        foreach ($data as &$value) {
            $value = StrUtil::characet($value, $this->config->charset);
        }

        $data['sign'] = $sign;// sign  需要放在末尾
        return http_build_query($data);
    }
}
