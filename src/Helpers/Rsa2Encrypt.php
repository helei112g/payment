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
 * Class Rsa2Encrypt
 * @package Payment\Helpers
 * @author  : Leo
 * @date    : 2019/3/30 8:12 PM
 * @version : 1.0.0
 * @desc    : rsa2加密算法
 *
 */
class Rsa2Encrypt
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * 设置key
     * @param $key
     * @author Leo
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * RSA2签名, 此处秘钥是私有秘钥
     * @param string $data 签名的数组
     * @throws \Exception
     * @return string
     * @author Leo
     */
    public function encrypt($data)
    {
        if ($this->key === false) {
            return '';
        }

        $res = openssl_pkey_get_private($this->key);
        if (empty($res)) {
            throw new \Exception('您使用的私钥格式错误，请检查RSA私钥配置');
        }

        openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        openssl_free_key($res);

        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * RSA2解密 此处秘钥是用户私有秘钥
     * @param string $content 需要解密的内容，密文
     * @throws \Exception
     * @return string
     * @author Leo
     */
    public function decrypt($content)
    {
        if ($this->key === false) {
            return '';
        }

        $res = openssl_pkey_get_private($this->key);
        if (empty($res)) {
            throw new \Exception('您使用的私钥格式错误，请检查RSA私钥配置');
        }

        //用base64将内容还原成二进制
        $decodes = base64_decode($content);

        $str     = '';
        $dcyCont = '';
        foreach ($decodes as $n => $decode) {
            if (!openssl_private_decrypt($decode, $dcyCont, $res)) {
                echo '<br/>' . openssl_error_string() . '<br/>';
            }
            $str .= $dcyCont;
        }

        openssl_free_key($res);
        return $str;
    }

    /**
     * RSA2验签 ，此处的秘钥，是第三方公钥
     * @param string $data 待签名数据
     * @param string $sign 要校对的的签名结果
     * @throws \Exception
     * @return bool
     * @author Leo
     */
    public function rsaVerify($data, $sign)
    {
        // 初始时，使用公钥key
        $res = openssl_get_publickey($this->key);
        if (empty($res)) {
            throw new \Exception('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        }

        $result = (bool) openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        openssl_free_key($res);
        return $result;
    }
}
