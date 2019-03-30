<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\Alipay;

use Payment\Exceptions\GatewayException;
use Payment\Helpers\ArrayUtil;
use Payment\Helpers\Rsa2Encrypt;
use Payment\Helpers\RsaEncrypt;
use Payment\Helpers\StrUtil;
use Payment\Payment;
use Payment\Supports\BaseObject;

/**
 * @package Payment\Gateways\Alipay
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/30 8:44 PM
 * @version : 1.0.0
 * @desc    :
 **/
abstract class AliBaseObject extends BaseObject
{
    /**
     * @var string
     */
    protected $privateKey = '';

    /**
     * @var string
     */
    protected $publicKey = '';

    /**
     * @var string
     */
    protected $gatewayUrl = '';

    /**
     * @var bool
     */
    protected $isSandbox = false;

    /**
     * @var bool
     */
    protected $returnRaw = false;

    /**
     * AliBaseObject constructor.
     * @throws GatewayException
     */
    public function __construct()
    {
        $this->isSandbox = self::$config->get('use_sandbox', false);
        $this->returnRaw = self::$config->get('return_raw', false);

        // 新版本，需要提供独立的支付宝公钥信息。每一个应用，公钥都不相同
        $rsaPublicKey = self::$config->get('ali_public_key', '');
        if ($rsaPublicKey) {
            $this->publicKey = StrUtil::getRsaKeyValue($rsaPublicKey, 'public');
        }
        if (empty($this->publicKey)) {
            throw new GatewayException('please set ali public key', Payment::PARAMS_ERR);
        }

        // 初始 RSA私钥文件 需要检查该文件是否存在
        $rsaPrivateKey = self::$config->get('rsa_private_key', '');
        if ($rsaPrivateKey) {
            $this->privateKey = StrUtil::getRsaKeyValue($rsaPrivateKey, 'private');
        }
        if (empty($this->privateKey)) {
            throw new GatewayException('please set ali private key', Payment::PARAMS_ERR);
        }

        // 初始 支付宝网关地址
        $this->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        }
    }

    /**
     * @param string $signType
     * @param string $signStr
     * @return string
     * @throws GatewayException
     */
    protected function makeSign(string $signType, string $signStr)
    {
        try {
            switch ($signType) {
                case 'RSA':
                    $rsa = new RsaEncrypt($this->privateKey);

                    $sign = $rsa->encrypt($signStr);
                    break;
                case 'RSA2':
                    $rsa = new Rsa2Encrypt($this->privateKey);

                    $sign = $rsa->encrypt($signStr);
                    break;
                default:
                    throw new GatewayException('ali pay sign type empty', Payment::PARAMS_ERR);
            }
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new GatewayException(sprintf('sign error, sign type is [%s].', $signType), Payment::SIGN_ERR);
        }

        return $sign;
    }

    /**
     * 获取基础数据
     * @param string $method
     * @param array $bizContent
     * @return array
     */
    protected function getBaseData(string $method, array $bizContent)
    {
        $requestData = [
            'app_id'     => self::$config->get('app_id', ''),
            'method'     => $method,
            'format'     => 'JSON',
            'return_url' => self::$config->get('return_url', ''),
            'charset'    => 'UTF-8',
            'sign_type'  => self::$config->get('sign_type', ''),
            'timestamp'  => date('Y-m-d H:i:s'),
            'version'    => '1.0',
            'notify_url' => self::$config->get('notify_url', ''),
            // 暂时不用
            // 'app_auth_token' => '',
            'biz_content' => json_encode($bizContent, JSON_UNESCAPED_UNICODE),
        ];
        return ArrayUtil::arraySort($requestData);
    }
}
