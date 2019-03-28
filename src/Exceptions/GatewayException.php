<?php

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
     * @param array $raw
     */
    public function __construct(string $message, int $code, array $raw = [])
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
