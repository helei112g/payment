<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 16/7/31
 * Time: 上午8:12
 */

namespace Payment\Utils;

/**
 * Class StrUtil
 * @dec 字符串处理类
 * @package Payment\Utils
 */
class StrUtil
{
    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++)  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
}