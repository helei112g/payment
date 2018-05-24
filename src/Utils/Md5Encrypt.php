<?php
/**
 * Created by ChaXian.
 * User: Bing
 * Date: 2018/5/24
 * Time: 11:07
 */

namespace Payment\Utils;


class Md5Encrypt
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function encrypt($data)
    {
        return md5($data.$this->key);
    }


}