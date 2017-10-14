<?php
namespace Payment\Charge\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\WebChargeData;

/**
 * @author: helei
 * @createTime: 2016-07-14 17:56
 * @description: 支付宝 即时到账 接口
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class AliWebCharge
 * @package Payment\Charge\Ali
 */
class AliWebCharge extends AliBaseStrategy
{
    // web 支付接口名称
    protected $method = 'alipay.trade.page.pay';

    /**
     * 获取支付对应的数据完成类
     * @return string
     * @author helei
     */
    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
        // 以下两种方式均可以
        return WebChargeData::class;
        //return 'Payment\Common\Ali\Data\Charge\WebChargeData';
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
