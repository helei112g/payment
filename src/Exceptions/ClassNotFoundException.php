<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Exceptions;

/**
 * @package Payment\Exceptions
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/30 3:30 PM
 * @version : 1.0.0
 * @desc    :
 **/
class ClassNotFoundException extends \RuntimeException
{
    /**
     * GatewayErrorException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
