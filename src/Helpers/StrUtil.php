<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Helpers;

/**
 * Class StrUtil
 * @package Payment\Helpers
 * @author  : Leo
 * @date    : 2019/3/30 8:12 PM
 * @version : 1.0.0
 * @desc    : 字符串处理类
 *
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
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str   = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
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
        for ($i = 0; $i < $len; $i++) {
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
        if (empty($keyStr)) {
            return null;
        }

        $keyStr = str_replace(PHP_EOL, '', $keyStr);
        // 为了解决用户传入的密钥格式，这里进行统一处理
        if ($type === 'private') {
            $beginStr = '-----BEGIN RSA PRIVATE KEY-----' . PHP_EOL;
            $endStr   = PHP_EOL . '-----END RSA PRIVATE KEY-----';
        } else {
            $beginStr = '-----BEGIN PUBLIC KEY-----' . PHP_EOL;
            $endStr   = PHP_EOL . '-----END PUBLIC KEY-----';
        }

        $rsaKey = $beginStr . wordwrap($keyStr, 64, "\n", true) . $endStr;

        return $rsaKey;
    }

    /**
     * 生成测试使用的二维码
     * @param $text
     * @return string
     */
    public static function toQRImg($text)
    {
        $chl = urlencode($text);

        return "https://cli.im/api/qrcode/code?text={$chl}&mhid=tBHED17pz8shMHcmKdxROK4";
    }
}
