<?php
namespace Payment\Charge\Ccb;

use Payment\Common\Ccb\CmbBaseStrategy;
use Payment\Common\Ccb\Data\Charge\ChargeData;

/**
 * 建设银行  接口
 *
 * Created by PhpStorm.
 * User: zhoujing
 * Date: 2018/5/8
 * Time: 下午12:36
 *
 * @link    http://121.15.180.72/OpenAPI2/DOC/ToDevelop5.aspx
 */
class CcbCharge extends CcbBaseStrategy
{
    public function getBuildDataClass()
    {
        $this->config->getewayUrl = 'https://ibsbjstar.ccb.com.cn/CCBIS/ccbMain';
        /*
        if ($this->config->useSandbox) {// 测试
            $this->config->getewayUrl = 'http://121.15.180.66:801/NetPayment/BaseHttp.dll?MB_EUserPay';
        }*/

        return ChargeData::class;
    }
}
