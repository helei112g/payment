<?php
namespace Payment\Common;

/**
 * @author: helei
 * @createTime: 2016-07-28 16:45
 * @description: 所有的策略类接口
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
interface BaseStrategy
{
    /**
     * 处理具体的业务
     * @param array $data
     * @return mixed
     * @author helei
     */
    public function handle(array $data);

    /**
     * 获取支付对应的数据完成类
     * @return BaseData
     * @author helei
     */
    public function getBuildDataClass();
}
