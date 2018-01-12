<?php
namespace Payment\Charge\Cmb;

use Payment\Common\Cmb\CmbBaseStrategy;
use Payment\Common\Cmb\Data\Charge\ChargeData;

/**
 * 一网通支付API  接口
 *
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/27
 * Time: 下午12:36
 *
 * @link    http://121.15.180.72/OpenAPI2/DOC/ToDevelop5.aspx
 */
class CmbCharge extends CmbBaseStrategy
{
    public function getBuildDataClass()
    {
        $this->config->getewayUrl = 'https://netpay.cmbchina.com/netpayment/BaseHttp.dll?MB_EUserPay';
        if ($this->config->useSandbox) {// 测试
            $this->config->getewayUrl = 'http://121.15.180.66:801/NetPayment/BaseHttp.dll?MB_EUserPay';
        }

        return ChargeData::class;
    }
}
