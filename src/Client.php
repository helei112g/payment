<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment;

use InvalidArgumentException;
use Payment\Exceptions\ClassNotFoundException;
use Payment\Exceptions\GatewayException;
use Payment\Supports\ProxyFactory;

/**
 * @package Payment
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/28 10:20 PM
 * @version : 1.0.0
 * @desc    : 客户端类
 * @method array pay(string $channel, array $requestParams) 请求支付
 **/
class Client
{
    const ALIPAY = 'Alipay'; // 支付宝

    const CCB = 'CCBank';// 建设银行

    const CMB = 'CMBank';// 招商银行

    const WECHAT = 'Wechat';// 微信

    /*----------------支付宝--------------------*/
    const ALI_CHANNEL_APP = 'app';// 支付宝 手机app 支付

    const ALI_CHANNEL_WAP = 'wap';// 支付宝 手机网页 支付

    const ALI_CHANNEL_WEB = 'web';// 支付宝 PC 网页支付

    const ALI_CHANNEL_QR = 'qr';// 支付宝 扫码支付

    const ALI_CHANNEL_BAR = 'bar';// 支付宝 条码支付

    /*----------------微信--------------------*/
    const WX_CHANNEL_APP = 'app';// 微信 APP 支付

    const WX_CHANNEL_PUB = 'pub';// 微信 公众账号 支付

    const WX_CHANNEL_QR = 'qr';// 微信 扫码支付  (可以使用app的帐号，也可以用公众的帐号完成)

    const WX_CHANNEL_BAR = 'bar';// 微信 刷卡支付，与支付宝的条码支付对应

    const WX_CHANNEL_LITE = 'lite';// 微信小程序支付

    const WX_CHANNEL_WAP = 'wap';// 微信wap支付，针对特定用户

    /*----------------招行--------------------*/
    const CMB_CHANNEL_APP = 'app';// 招商 app  ，实际上招商并无该概念

    const CMB_CHANNEL_WAP = 'wap';// 招商h5支付，其实app支付也是使用的h5

    /*----------------建行--------------------*/
    const CCB_CHANNEL_WEB = 'web';//pc 支付

    /**
     * @var null|Supports\BaseObject
     */
    protected $proxy = null;

    /**
     * 初始化
     * Client constructor.
     * @param string $proxy
     * @param array $config
     */
    public function __construct(string $proxy, array $config)
    {
        $this->proxy = $this->getProxyInstance($proxy);
        $this->proxy->setConfig($config);
    }

    /**
     * 访问静态方法
     * @param string $name
     * @param $arguments
     * @return mixed
     * @throws InvalidArgumentException
     * @throws ClassNotFoundException
     * @throws GatewayException
     */
    public function __call($name, $arguments)
    {
        // 获取函数参数信息
        try {
            if (!method_exists($this->proxy, $name)) {
                throw new InvalidArgumentException(sprintf('[%s] method is not exist in proxy [%s].', $name, $this->proxy->className()), Payment::PARAMS_ERR);
            }

            $reflect     = new \ReflectionMethod($this->proxy, $name);
            $params      = $reflect->getParameters();
            $countParams = count($params);
            if ($countParams !== count($arguments)) {
                throw new InvalidArgumentException(sprintf('[%s] method need [%d] params.', $name, $countParams), Payment::PARAMS_ERR);
            }
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException(sprintf('[%s] class not found.', $this->proxy->className()), Payment::CLASS_NOT_EXIST);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        try {
            return call_user_func_array([$this->proxy, $name], $arguments);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 获取代理实例
     * @param string $proxy
     * @return Supports\BaseObject
     * @throws InvalidArgumentException
     */
    protected function getProxyInstance(string $proxy)
    {
        return ProxyFactory::createProxy($proxy);
    }
}
