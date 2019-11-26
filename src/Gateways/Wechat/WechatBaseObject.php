<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\Wechat;

use Payment\Exceptions\GatewayException;
use Payment\Helpers\ArrayUtil;
use Payment\Helpers\DataParser;
use Payment\Helpers\StrUtil;
use Payment\Payment;
use Payment\Supports\BaseObject;
use Payment\Supports\HttpRequest;

/**
 * @package Payment\Gateways\Wechat
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/4/7 8:55 AM
 * @version : 1.0.0
 * @desc    : 微信网络请求基类
 **/
abstract class WechatBaseObject extends BaseObject
{
    use HttpRequest;

    const NONCE_LEN = 32;// 随机字符串长度

    const REQ_SUC = 'SUCCESS';

    /**
     * @var string
     */
    protected $gatewayUrl = '';

    /**
     * @var string
     */
    protected $md5Key = '';

    /**
     * @var string
     */
    private $sandboxKey = '';

    /**
     * @var bool
     */
    protected $isSandbox = false;

    /**
     * @var bool
     */
    protected $returnRaw = false;

    /**
     * @var string
     */
    protected $nonceStr = '';

    /**
     * @var bool
     */
    protected $useBackup = false;

    /**
     * WechatBaseObject constructor.
     * @throws GatewayException
     */
    public function __construct()
    {
        $this->isSandbox = self::$config->get('use_sandbox', false);
        $this->useBackup = self::$config->get('use_backup', false);
        $this->returnRaw = self::$config->get('return_raw', false);
        $this->md5Key    = self::$config->get('md5_key', '');
        $this->nonceStr  = StrUtil::getNonceStr(self::NONCE_LEN);

        // 初始 微信网关地址
        $this->gatewayUrl = 'https://api.mch.weixin.qq.com/%s';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'https://apitest.mch.weixin.qq.com/sandboxnew/%s';
        } elseif ($this->useBackup) {
            $this->gatewayUrl = 'https://api2.mch.weixin.qq.com/%s'; // 灾备地址
        }

        // 如果是沙盒模式，更换密钥
        if ($this->isSandbox && empty($this->sandboxKey)) {
            $this->sandboxKey = $this->getSignKey();
            $this->md5Key     = $this->sandboxKey;
        }

        if ($this instanceof Settlement) {// 资金结算单只支持该方式
            self::$config['sign_type'] = 'HMAC-SHA256';
        }
    }

    /**
     * 生成请求参数
     * @param array $requestParams
     * @return string
     * @throws GatewayException
     */
    protected function buildParams(array $requestParams = [])
    {
        $signType = self::$config->get('sign_type', '');
        $params   = [
            //'appid'     => self::$config->get('app_id', ''),
            'mch_id'    => self::$config->get('mch_id', ''),
            'nonce_str' => $this->nonceStr,
            'sign_type' => $signType,
        ];

        if (!empty($requestParams)) {
            $selfParams = $this->getSelfParams($requestParams);

            if (is_array($selfParams) && !empty($selfParams)) {
                $params = array_merge($params, $selfParams);
            }
        }

        $params = ArrayUtil::paraFilter($params);
        $params = ArrayUtil::arraySort($params);

        try {
            $signStr        = ArrayUtil::createLinkstring($params);
            $params['sign'] = $this->makeSign($signType, $signStr);
        } catch (\Exception $e) {
            throw new GatewayException($e->getMessage(), Payment::PARAMS_ERR);
        }

        $xmlData = DataParser::toXml($params);
        if ($xmlData === false) {
            throw new GatewayException('error generating xml', Payment::FORMAT_DATA_ERR);
        }

        return $xmlData;
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    abstract protected function getSelfParams(array $requestParams);

    /**
     * 签名算法实现  便于后期扩展微信不同的加密方式
     * @param string $signType
     * @param string $signStr
     * @return string
     * @throws GatewayException
     */
    protected function makeSign(string $signType, string $signStr)
    {
        try {
            switch ($signType) {
                case 'MD5':
                    $signStr .= '&key=' . $this->md5Key;
                    $sign = md5($signStr);
                    break;
                case 'HMAC-SHA256':
                    $signStr .= '&key=' . $this->md5Key;
                    $sign = strtoupper(hash_hmac('sha256', $signStr, $this->md5Key));
                    break;
                default:
                    throw new GatewayException(sprintf('[%s] sign type not support', $signType), Payment::PARAMS_ERR);
            }
        } catch (GatewayException $e) {
            throw $e;
        }

        return strtoupper($sign);
    }

    /**
     * 检查微信返回的数据是否被篡改过
     * @param array $retData
     * @return boolean
     * @author helei
     * @throws GatewayException
     */
    protected function verifySign(array $retData)
    {
        $signType = strtoupper(self::$config->get('sign_type', ''));
        if ($this instanceof Settlement) {// 资金结算单只支持该方式
            $signType = 'HMAC-SHA256';
        }

        try {
            $retSign = $retData['sign'];
            $values  = ArrayUtil::removeKeys($retData, ['sign', 'sign_type']);
            $values  = ArrayUtil::paraFilter($values);
            $values  = ArrayUtil::arraySort($values);
            $signStr = ArrayUtil::createLinkstring($values);
        } catch (\Exception $e) {
            throw new GatewayException('wechat verify sign generate str get error', Payment::SIGN_ERR);
        }

        $signStr .= '&key=' . $this->md5Key;
        switch ($signType) {
            case 'MD5':
                $sign = md5($signStr);
                break;
            case 'HMAC-SHA256':
                $sign = hash_hmac('sha256', $signStr, $this->md5Key);
                break;
            default:
                $sign = '';
        }
        return strtoupper($sign) === $retSign;
    }

    /**
     * 获取证书参数
     * @return array
     */
    protected function getCertOptions()
    {
        $basePath = $this->getBasePath();
        return [
            'cert'    => self::$config->get('app_cert_pem', ''),
            'ssl_key' => self::$config->get('app_key_pem', ''),
            'verify'  => $basePath . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'wx_cacert.pem',
        ];
    }

    /**
     * @throws GatewayException
     */
    protected function getSignKey()
    {
        try {
            $xmlData = $this->buildParams();
            $url     = sprintf($this->gatewayUrl, 'pay/getsignkey');

            $this->setHttpOptions($this->getCertOptions());
            $resXml = $this->postXML($url, $xmlData);

            $resArr = DataParser::toArray($resXml);
            if (!is_array($resArr) || $resArr['return_code'] !== self::REQ_SUC) {
                throw new GatewayException($this->getErrorMsg($resArr), Payment::GATEWAY_REFUSE, $resArr);
            }

            return $resArr['sandbox_signkey'];
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 获取微信的错误信息，微信自己垃圾没有兼容好字段
     * @param mixed $resArr
     * @return string
     */
    protected function getErrorMsg($resArr)
    {
        if (!is_array($resArr)) {
            return 'not array';
        }
        return isset($resArr['retmsg']) ? $resArr['retmsg'] : (isset($resArr['return_msg']) ? $resArr['return_msg'] : 'error');
    }
}
