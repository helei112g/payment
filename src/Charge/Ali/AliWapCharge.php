<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:19
 * @description: 支付宝 手机网站支付 接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Ali;


use Payment\Common\Ali\Data\Charge\WapChargeData;

class AliWapCharge extends AliCharge
{

    public function charge(array $data)
    {
        $pay = new WapChargeData($this->config, $data);

        $pay->setSign();

        $data = $pay->getData();
        $url = $this->config->getewayUrl . http_build_query($data);
        return $url;
    }
}