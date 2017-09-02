<?php
namespace Payment\Common\Ali\Data\Query;


use Payment\Common\Ali\Data\AliBaseData;
use Payment\Utils\ArrayUtil;

/**
 * 查询数据基类
 * Class QueryBaseData
 * @package Payment\Common\Ali\Data\Query
 */
abstract class QueryBaseData extends AliBaseData
{
    /**
     * 构建 APP支付 加密数据
     * @author helei
     */
    protected function buildData()
    {
        $bizContent = $this->getBizContent();
        $bizContent = ArrayUtil::paraFilter($bizContent);// 过滤掉空值，下面不用在检查是否为空

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
            'biz_content'   => json_encode($bizContent, JSON_UNESCAPED_UNICODE),
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