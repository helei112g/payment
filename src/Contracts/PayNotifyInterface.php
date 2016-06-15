<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 14:43
 * @description: 所有的异步通知业务逻辑，必须继承该接口。
 */

namespace Payment\Contracts;


interface PayNotifyInterface
{
    /**
     * 回调方法入口，子类可重写该方法
     *  里边应包含自己的业务逻辑。
     * @param array $data
     * @return bool
     * @author helei
     */
    public function notifyProcess($data);
}