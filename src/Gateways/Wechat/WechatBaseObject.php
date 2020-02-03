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

    const SIGN_TYPE_MD5 = 'MD5';

    const SIGN_TYPE_SHA = 'HMAC-SHA256';

    /**
     * @var string
     */
    protected $gatewayUrl = '';

    /**
     * @var string
     */
    protected $merKey = '';

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
     * 设置加密方式
     * @var string
     */
    protected $signType = '';

    /**
     * 请求方法的名称
     * @var string
     */
    protected $methodName = '';

    /**
     * WechatBaseObject constructor.
     * @throws GatewayException
     */
    public function __construct()
    {
        $this->isSandbox = self::$config->get('use_sandbox', false);
        $this->useBackup = self::$config->get('use_backup', false);
        $this->returnRaw = self::$config->get('return_raw', false);
        $this->merKey    = self::$config->get('md5_key', '');
        $this->signType  = self::$config->get('sign_type', '');
        $this->nonceStr  = StrUtil::getNonceStr(self::NONCE_LEN);

        // 初始 微信网关地址
        $this->gatewayUrl = 'https://api.mch.weixin.qq.com/%s';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'https://api.mch.weixin.qq.com/sandboxnew/%s';
        } elseif ($this->useBackup) {
            $this->gatewayUrl = 'https://api2.mch.weixin.qq.com/%s'; // 灾备地址
        }

        // 如果是沙盒模式，更换密钥
        if ($this->isSandbox && empty($this->sandboxKey)) {
            $this->sandboxKey = $this->getSignKey();
            //$this->sandboxKey = 'c15772692e55c8db69b40d1cb8e6f627';
            $this->merKey = $this->sandboxKey;
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
        $params = [
            'appid'      => self::$config->get('app_id', ''),
            'sub_appid'  => self::$config->get('sub_appid', ''),
            'mch_id'     => self::$config->get('mch_id', ''),
            'sub_mch_id' => self::$config->get('sub_mch_id', ''),
            'nonce_str'  => $this->nonceStr,
            'sign_type'  => $this->signType,
        ];
        $params = $this->changeKeyName($params);

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
            $params['sign'] = $this->makeSign($signStr);
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
     * @param string $signStr
     * @return string
     * @throws GatewayException
     */
    protected function makeSign(string $signStr)
    {
        try {
            switch ($this->signType) {
                case self::SIGN_TYPE_MD5:
                    $signStr .= '&key=' . $this->merKey;
                    $sign = md5($signStr);
                    break;
                case self::SIGN_TYPE_SHA:
                    $signStr .= '&key=' . $this->merKey;
                    $sign = strtoupper(hash_hmac('sha256', $signStr, $this->merKey));
                    break;
                default:
                    throw new GatewayException(sprintf('[%s] sign type not support', $this->signType), Payment::PARAMS_ERR);
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
        try {
            $retSign = $retData['sign'];
            $values  = ArrayUtil::removeKeys($retData, ['sign', 'sign_type']);
            $values  = ArrayUtil::paraFilter($values);
            $values  = ArrayUtil::arraySort($values);
            $signStr = ArrayUtil::createLinkstring($values);
        } catch (\Exception $e) {
            throw new GatewayException('wechat verify sign generate str get error', Payment::SIGN_ERR);
        }

        $signStr .= '&key=' . $this->merKey;
        switch ($this->signType) {
            case self::SIGN_TYPE_MD5:
                $sign = md5($signStr);
                break;
            case self::SIGN_TYPE_SHA:
                $sign = hash_hmac('sha256', $signStr, $this->merKey);
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
        $method = 'pay/getsignkey';
        try {
            $resArr = $this->requestWXApi($method, []);

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

    /**
     * 请求微信支付的api
     * @param string $method
     * @param array $requestParams
     * @return array|false
     * @throws GatewayException
     */
    protected function requestWXApi(string $method, array $requestParams)
    {
        $this->methodName = $method;
        try {
            $xmlData = $this->buildParams($requestParams);
            $url     = sprintf($this->gatewayUrl, $method);

            $this->setHttpOptions($this->getCertOptions());
            $resXml = $this->postXML($url, $xmlData);
            if (in_array($method, ['pay/downloadbill', 'pay/downloadfundflow'])) {
                return $resXml;
            }

            $resArr = DataParser::toArray($resXml);
            if (!is_array($resArr) || $resArr['return_code'] !== self::REQ_SUC) {
                throw new GatewayException($this->getErrorMsg($resArr), Payment::GATEWAY_REFUSE, $resArr);
            } elseif (isset($resArr['result_code']) && $resArr['result_code'] !== self::REQ_SUC) {
                throw new GatewayException(sprintf('code:%d, desc:%s', $resArr['err_code'], $resArr['err_code_des']), Payment::GATEWAY_CHECK_FAILED, $resArr);
            }

            if (isset($resArr['sign']) && $this->verifySign($resArr) === false) {
                throw new GatewayException('check return data sign failed', Payment::SIGN_ERR, $resArr);
            }

            return $resArr;
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 修改关键key的名字
     * @param array $params
     * @return array
     */
    protected function changeKeyName(array $params)
    {
        $changeMap = [
            'mmpaymkttransfers/promotion/transfers',
            'mmpaymkttransfers/sendredpack',
        ];

        if (!in_array($this->methodName, $changeMap)) {
            return $params;
        }

        if ($this->methodName === 'mmpaymkttransfers/promotion/transfers') {
            $params['mch_appid'] = $params['appid'];
            $params['mchid']     = $params['mch_id'];
            unset($params['appid']);
        } elseif ($this->methodName === 'mmpaymkttransfers/sendredpack') {
            unset($params['appid']);
            $params['wxappid'] = self::$config->get('app_id', '');
        }

        return $params;
    }

    /**
     * @param string $gatewayUrl
     */
    protected function setGatewayUrl(string $gatewayUrl)
    {
        $this->gatewayUrl = $gatewayUrl;
    }

    /**
     * @param string $signType
     */
    protected function setSignType(string $signType)
    {
        $this->signType = $signType;
    }
}
