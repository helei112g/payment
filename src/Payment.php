<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment;

/**
 * @package Payment
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/30 3:32 PM
 * @version : 1.0.0
 * @desc    : 系统常量
 **/
final class Payment
{
    const SUC = 0;

    // 相关操作错误
    const CLASS_NOT_EXIST    = 1000;
    const PARAMS_ERR         = 1001;
    const NOT_SUPPORT_METHOD = 1002;

    const SIGN_ERR        = 2001;
    const FORMAT_DATA_ERR = 2002;
}
