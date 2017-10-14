<?php
namespace Payment\Charge\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\AppChargeData;

/**
 * @author: helei
 * @createTime: 2016-07-14 18:20
 * @description: 支付宝移动支付接口
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class AliAppCharge extends AliBaseStrategy
{
    // app 支付接口名称
    protected $method = 'alipay.trade.app.pay';

    /**
     * 获取支付对应的数据完成类
     * @return string
     * @author helei
     */
    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
        // 以下两种方式任选一种
        return AppChargeData::class;
    }

    /**
     * 组装返回的数据格式
     * @param array $data
     * @return string
     */
    protected function retData(array $data)
    {
        $data = parent::retData($data);

        // 组装成 key=value&key=value 形式返回
        return http_build_query($data);
    }
}
