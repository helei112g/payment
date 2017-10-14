<?php
namespace Payment;

use Payment\Notify\AliNotify;
use Payment\Notify\CmbNotify;
use Payment\Notify\NotifyStrategy;
use Payment\Notify\PayNotifyInterface;
use Payment\Notify\WxNotify;
use Payment\Common\PayException;

/**
 * 异步通知的上下文
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class NotifyContext
 * @package Payment
 */
class NotifyContext
{
    /**
     * 支付的渠道
     * @var NotifyStrategy
     */
    protected $notify;


    /**
     * 设置对应的通知渠道
     * @param string $channel 通知渠道
     *  - @see Config
     *
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initNotify($channel, array $config)
    {
        try {
            switch ($channel) {
                case Config::ALI_CHARGE:
                    $this->notify = new AliNotify($config);
                    break;
                case Config::WX_CHARGE:
                    $this->notify = new WxNotify($config);
                    break;
                case Config::CMB_CHARGE:
                    $this->notify = new CmbNotify($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI_CHARGE WX_CHARGE CMB_CHARGE 常量');
            }
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 返回异步通知的数据
     * @return array|false
     */
    public function getNotifyData()
    {
        return $this->notify->getNotifyData();
    }

    /**
     * 通过环境类调用支付异步通知
     *
     * @param PayNotifyInterface $notify
     * @return array
     * @throws PayException
     * @author helei
     */
    public function notify(PayNotifyInterface $notify)
    {
        if (! $this->notify instanceof NotifyStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        return $this->notify->handle($notify);
    }
}
