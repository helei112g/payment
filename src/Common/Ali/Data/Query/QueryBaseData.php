<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/6
 * Time: 下午10:37
 */

namespace Payment\Common\Ali\Data\Query;


use Payment\Common\Ali\Data\AliBaseData;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

abstract class QueryBaseData extends AliBaseData
{
    /**
     * 构建 APP支付 加密数据
     * @author helei
     */
    protected function buildData()
    {
        $signData = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'charset'       => $this->charset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,

            // 业务参数
            'biz_content'   => $this->getBizContent(),
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 支付宝构建请求查询的数据
     * @return mixed
     */
    abstract protected function getBizContent();
}