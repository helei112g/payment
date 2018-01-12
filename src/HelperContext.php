<?php
namespace Payment;

use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Helper\Cmb\BindCardHelper;
use Payment\Helper\Cmb\PubKeyHelper;

/**
 * 用于完成一些辅助操作，例如： 招商绑卡  操作
 * Class HelperContext
 * @package Payment
 */
class HelperContext
{
    /**
     * 转款渠道
     * @var BaseStrategy
     */
    protected $helper;

    /**
     * 设置对应的退款渠道
     * @param string $way 对应的方式渠道
     *  - @see Config
     *
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initHelper($way, array $config)
    {
        try {
            switch ($way) {
                case Config::CMB_BIND:
                    $this->helper = new BindCardHelper($config);
                    break;
                case Config::CMB_PUB_KEY:
                    $this->helper = new PubKeyHelper($config);
                    break;
                default:
                    throw new PayException('当前仅支持：CMB_BIND CMB_PUB_KEY 操作');
            }
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 获取帮助操作
     *
     * @param array $data
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function helper(array $data)
    {
        if (! $this->helper instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->helper->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}
