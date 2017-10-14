<?php
namespace Payment;

use Payment\Charge\Ali\AliAppCharge;
use Payment\Charge\Ali\AliBarCharge;
use Payment\Charge\Ali\AliWapCharge;
use Payment\Charge\Ali\AliWebCharge;
use Payment\Charge\Ali\AliQrCharge;
use Payment\Charge\Cmb\CmbCharge;
use Payment\Charge\Wx\WxAppCharge;
use Payment\Charge\Wx\WxBarCharge;
use Payment\Charge\Wx\WxPubCharge;
use Payment\Charge\Wx\WxQrCharge;
use Payment\Charge\Wx\WxWapCharge;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;

/**
 * @author: helei
 * @createTime: 2016-07-14 17:42
 * @description: 支付上下文
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class ChargeContext
 *
 * 支付的上下文类
 *
 * @package Payment
 */
class ChargeContext
{
    /**
     * 支付的渠道
     * @var BaseStrategy
     */
    protected $channel;


    /**
     * 设置对应的支付渠道
     * @param string $channel 支付渠道
     *  - @see Config
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initCharge($channel, array $config)
    {
        // 初始化时，可能抛出异常，再次统一再抛出给客户端进行处理
        try {
            switch ($channel) {
                case Config::ALI_CHANNEL_WAP:
                    $this->channel = new AliWapCharge($config);
                    break;
                case Config::ALI_CHANNEL_APP:
                    $this->channel = new AliAppCharge($config);
                    break;
                case Config::ALI_CHANNEL_WEB:
                    $this->channel = new AliWebCharge($config);
                    break;
                case Config::ALI_CHANNEL_QR:
                    $this->channel = new AliQrCharge($config);
                    break;
                case Config::ALI_CHANNEL_BAR:
                    $this->channel = new AliBarCharge($config);
                    break;

                case Config::WX_CHANNEL_APP:
                    $this->channel = new WxAppCharge($config);
                    break;
                case Config::WX_CHANNEL_LITE:// 小程序支付与公众号支付一样，仅仅是客户端的调用方式不同
                case Config::WX_CHANNEL_PUB:
                    $this->channel = new WxPubCharge($config);
                    break;
                case Config::WX_CHANNEL_WAP:
                    $this->channel = new WxWapCharge($config);
                    break;
                case Config::WX_CHANNEL_QR:
                    $this->channel = new WxQrCharge($config);
                    break;
                case Config::WX_CHANNEL_BAR:
                    $this->channel = new WxBarCharge($config);
                    break;

                case Config::CMB_CHANNEL_WAP:
                case Config::CMB_CHANNEL_APP:
                    $this->channel = new CmbCharge($config);
                    break;
                default:
                    throw new PayException('当前仅支持：支付宝  微信 招商一网通');
            }
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 通过环境类调用支付
     * @param array $data
     *
     * ```php
     * $payData = [
     *      "order_no" => createPayid(),
     *      "amount" => '0.01',// 单位为元 ,最小为0.01
     *      "client_ip" => '127.0.0.1',
     *      "subject" => '测试支付',
     *      "body" => '支付接口测试',
     *      "extra_param"   => '',
     * ];
     * ```
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function charge(array $data)
    {
        if (! $this->channel instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->channel->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}
