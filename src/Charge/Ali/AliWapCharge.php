<?php
namespace Payment\Charge\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\WapChargeData;

/**
 * @author: helei
 * @createTime: 2016-07-14 18:19
 * @description: 支付宝 手机网站支付 接口
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class AliWapCharge extends AliBaseStrategy
{
    // wap 支付接口名称
    protected $method = 'alipay.trade.wap.pay';

    /**
     * 获取支付对应的数据完成类
     * @return string
     * @author helei
     */
    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
        // 以下两种方式任选一种
        return WapChargeData::class;
    }

    /**
     * 返回可发起h5支付的请求
     * @param array $data
     * @return array|string
     */
    protected function retData(array $data)
    {
        $data = parent::retData($data);

        return $this->config->getewayUrl . '?' . http_build_query($data);
    }
}
