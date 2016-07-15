<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 14:56
 * @description:
 */

namespace Payment\Common;


use Payment\Utils\ArrayUtil;

final class AliConfig extends ConfigInterface
{
    // 支付宝的网关
    public $getewayUrl = 'https://mapi.alipay.com/gateway.do?';

    // 采用的编码
    public $inputCharset = 'utf-8';

    // 合作者身份ID
    public $partner;

    // 用于加密的md5Key
    public $md5Key;

    // 卖家支付宝账号 邮箱或手机号码格式
    public $sellerId;

    // 用于rsa加密的私钥文件路径
    public $rsaPrivatePath;

    // 用于rsa解密的支付宝公钥文件路径
    public $rsaAliPubPath;

    // 安全证书的路径
    public $cacertPath;

    public function __construct(array $config)
    {
        // 初始化配置信息
        $this->initConfig($config);

        $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Ali' . DIRECTORY_SEPARATOR;
        $this->rsaAliPubPath = "{$basePath}alipay_public_key.pem";
        $this->cacertPath = "{$basePath}cacert.pem";
    }

    /**
     * 检查传入的配置文件信息是否正确
     * @param array $config
     * @throws PayException
     * @author helei
     */
    private function initConfig(array $config)
    {
        $config = ArrayUtil::paraFilter($config);

        // 初始 合作者身份ID
        if (key_exists('partner', $config) && strlen($config['partner']) == '16') {
            $this->partner = $config['partner'];
        } else {
            throw new PayException('合作者身份ID 以2088开头的16位纯数字组成');
        }

        // 初始 MD5 key
        if (key_exists('md5_key', $config) && !empty($config['md5_key'])) {
            $this->md5Key = $config['md5_key'];
        } else {
            throw new PayException('MD5 Key 不能为空，再支付宝后台可查看');
        }

        // 初始 卖家支付宝账号
        if (key_exists('seller_id', $config) && !empty($config['seller_id'])) {
            $this->sellerId = $config['seller_id'];
        } else {
            throw new PayException('卖家支付宝账号 邮箱或手机号码格式');
        }

        // 初始 RSA私钥文件 需要检查该文件是否存在
        if (key_exists('rsa_private_key', $config) && file_exists($config['rsa_private_key'])) {
            $this->rsaPrivatePath = $config['rsa_private_key'];
        } else {
            throw new PayException('RSA私钥文件 不能为空，请确保在该路径下存在');
        }

        // 初始 字符编码 默认使用utf-8
        if (key_exists('input_charset', $config) && !empty($config['input_charset'])) {
            $this->inputCharset = $config['input_charset'];
        }

        // 初始 支付宝网关地址
        if (key_exists('geteway_url', $config) && !empty($config['geteway_url'])) {
            $this->getewayUrl = $config['geteway_url'];
        }
    }
}