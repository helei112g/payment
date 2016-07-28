<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 15:28
 * @description: 支付宝批量付款接口
 */

namespace Payment\Trans;


use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\TransData;

class AliTransfer extends AliBaseStrategy
{

    protected function getBuildDataClass()
    {
        return TransData::class;
    }
}