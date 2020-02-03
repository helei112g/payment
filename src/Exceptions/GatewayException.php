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
 * @package Payment\Exception
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/28 10:13 PM
 * @version : 1.0.0
 * @desc    :
 **/
class GatewayException extends \Exception
{
    /**
     * @var array
     */
    private $raw = [];

    /**
     * GatewayErrorException constructor.
     * @param string $message
     * @param int $code
     * @param mixed $raw
     */
    public function __construct(string $message, int $code, $raw = [])
    {
        parent::__construct($message, $code);

        $this->raw = $raw;
    }

    /**
     * @return array
     */
    public function getRaw()
    {
        return $this->raw;
    }
}
