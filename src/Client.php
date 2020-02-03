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
use Payment\Contracts\IPayNotify;
use Payment\Exceptions\ClassNotFoundException;
use Payment\Exceptions\GatewayException;
use Payment\Proxies\CMBankProxy;
use Payment\Supports\ProxyFactory;

/**
 * @package Payment
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/28 10:20 PM
 * @version : 1.0.0
 * @desc    : 客户端类
 * @method array pay(string $channel, array $requestParams) 请求支付
 * @method array refund(array $requestParams) 退款请求
 * @method array tradeQuery(array $requestParams) 交易查询
 * @method array refundQuery(array $requestParams) 退款查询
 * @method array transferQuery(array $requestParams) 转账查询
 * @method array billDownload(array $requestParams) 对账单下载
 * @method array settleDownload(array $requestParams) 结算单下载
 * @method array transfer(array $requestParams) 转账
 * @method array cancel(array $requestParams) 取消交易
 * @method array close(array $requestParams) 关闭交易
 * @method array notify(IPayNotify $callback) 异步通知
 **/
class Client
{
    /*----------------支持的渠道-----------------*/
    const ALIPAY = 'Alipay'; // 支付宝

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

    const WX_SETTLE_SINGLE = 'single'; // 单次分账

    const WX_SETTLE_MULTI = 'multi'; // 多次分账

    /*----------------招行--------------------*/
    const CMB_CHANNEL_APP = 'app';// 招商 app

    const CMB_CHANNEL_WAP = 'wap';// 招商h5支付

    const CMB_CHANNEL_WEB = 'web';// 招商pc

    const CMB_CHANNEL_QR = 'qr';// 招商qr

    const CMB_CHANNEL_LITE = 'lite';// 招商小程序

    /*----------------其它--------------------*/
    const TRANSFER_CHANNEL_BANK = 'bank';

    const TRANSFER_CHANNEL_ACCOUNT = 'account';


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
     * 访问方法
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
        try {
            return ProxyFactory::createProxy($proxy);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * 获取公钥
     * @throws GatewayException
     */
    public function getPubKey()
    {
        if (!$this->proxy instanceof CMBankProxy) {
            throw new GatewayException('just cmb support the method.', Payment::NOT_SUPPORT_METHOD);
        }

        return $this->proxy->getPubKey();
    }
}
