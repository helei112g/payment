<?php
namespace Payment\Helper\Cmb;

use Payment\Common\Cmb\CmbBaseStrategy;
use Payment\Common\Cmb\Data\BindCardData;

/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 下午12:01
 */
class BindCardHelper extends CmbBaseStrategy
{

    public function getBuildDataClass()
    {
        $this->config->getewayUrl = 'https://mobile.cmbchina.com/mobilehtml/DebitCard/M_NetPay/OneNetRegister/NP_BindCard.aspx';
        if ($this->config->useSandbox) {// 测试
            $this->config->getewayUrl = 'http://121.15.180.66:801/mobilehtml/DebitCard/M_NetPay/OneNetRegister/NP_BindCard.aspx';
        }

        return BindCardData::class;
    }
}