<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:56
 * @description: 支付宝 即时到账 接口
 */

namespace Payment\Charge\Ali;

use Payment\Common\Ali\Data\Charge\WebChargeData;
use Payment\Utils\ArrayUtil;

class AliWebCharge extends AliCharge
{
    /**
     * 支付的业务逻辑
     * @param array $data
     * @return array
     * @author helei
     */
    public function charge(array $data)
    {
        $pay = new WebChargeData($this->config, $data);

        $pay->setSign();

        return $pay->getData();
    }
}