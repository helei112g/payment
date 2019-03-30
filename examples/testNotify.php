<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Payment\Config;
use Payment\Notify\PayNotifyInterface;

/**
 * @author: Leo
 * @createTime: 2016-07-20 18:31
 * @description:
 */

/**
 * 客户端需要继承该接口，并实现这个方法，在其中实现对应的业务逻辑
 * Class TestNotify
 * anthor Leo
 */
class TestNotify implements PayNotifyInterface
{
    public function notifyProcess(array $data)
    {
        $channel = $data['channel'];
        if ($channel === Config::ALI_CHARGE) {// 支付宝支付
        } elseif ($channel === Config::WX_CHARGE) {// 微信支付
        } elseif ($channel === Config::CMB_CHARGE) {// 招商支付
        } elseif ($channel === Config::CMB_BIND) {// 招商签约
        }
        // 其它类型的通知


        // 执行业务逻辑，成功后返回true
        return true;
    }
}
