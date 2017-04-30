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
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 转码字符集转码  仅支持 转码 到 UTF-8
     * @param string $str
     * @param string $targetCharset
     * @return mixed|string
     */
    public static function characet($str, $targetCharset)
    {
        if (empty($str)) {
            return $str;
        }

        if (strcasecmp('UTF-8', $targetCharset) != 0) {
            $str = mb_convert_encoding($str, $targetCharset, 'UTF-8');
        }

        return $str;
    }

    /**
     * 转成16进制
     * @param string $string
     * @return string
     */
    public static function String2Hex($string)
    {
        $hex = '';
        $len = strlen($string);
        for ($i=0; $i < $len; $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    /**
     * 获取rsa密钥内容
     * @param string $key 传入的密钥信息， 可能是文件或者字符串
     * @param string $type
     *
     * @return string
     */
    public static function getRsaKeyValue($key, $type = 'private')
    {
        if (is_file($key)) {// 是文件
            $keyStr = @file_get_contents($key);
        } else {
            $keyStr = $key;
        }
        $keyStr = str_replace(PHP_EOL, '', $keyStr);
        // 为了解决用户传入的密钥格式，这里进行统一处理
        if ($type === 'private') {
            $beginStr = ['-----BEGIN RSA PRIVATE KEY-----', '-----BEGIN PRIVATE KEY-----'];
            $endStr = ['-----END RSA PRIVATE KEY-----', '-----END PRIVATE KEY-----'];
        } else {
            $beginStr = ['-----BEGIN PUBLIC KEY-----', ''];
            $endStr = ['-----END PUBLIC KEY-----', ''];
        }
        $keyStr = str_replace($beginStr, ['', ''], $keyStr);
        $keyStr = str_replace($endStr, ['', ''], $keyStr);

        $rsaKey = $beginStr[0] . PHP_EOL . wordwrap($keyStr, 64, PHP_EOL, true) . PHP_EOL . $endStr[0];

        return $rsaKey;
    }
}
