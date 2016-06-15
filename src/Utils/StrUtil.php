<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 13:39
 * @description:
 */

namespace Payment\Utils;


class StrUtil
{
    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @param string $attach 附加的字符
     * @return string 产生的随机字符串
     */
    public static function getNonceStr($length = 32, $attach = '')
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        if ($attach) {
            $chars .= $attach;
        }

        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
}