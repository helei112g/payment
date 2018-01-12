<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/29
 * Time: 上午9:52
 */

namespace Payment\Common\Cmb\Data;

use Payment\Common\CmbConfig;

/**
 * 获取招商的公钥
 * Class PubKeyData
 * @package Payment\Common\Cmb\Data
 */
class PubKeyData extends CmbBaseData
{
    protected function getReqData()
    {
        $reqData = [
            'dateTime' => $this->dateTime,
            'branchNo' => $this->branchNo,
            'merchantNo' => $this->merchantNo,
            'txCode' => CmbConfig::TRADE_CODE,
        ];

        // 这里不能进行过滤空值，招商的空值也要加入签名中
        return $reqData;
    }
}
