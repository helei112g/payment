<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:51
 * @description: 支付回调的策略接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Notify;


abstract class NotifyStrategy
{

    /**
     * 配置信息
     * @var array $config
     */
    protected $config;

    /**
     * NotifyStrategy constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");
    }

    /**
     * 主要任务，验证返回的数据是否正确
     * @param PayNotifyInterface $notify
     * @return mixed
     * @author helei
     */
    final public function handle(PayNotifyInterface $notify)
    {
        // 获取异步通知的数据
        $notifyData = $this->getNotifyData();
        if ($notifyData === false) {// 失败，就返回错误
            return $this->replyNotify(false, '获取通知数据失败');
        }

        // 检查异步通知返回的数据是否有误
        $checkRet = $this->checkNotifyData($notifyData);
        if ($checkRet === false) {// 失败，就返回错误
            return $this->replyNotify(false, '返回数据验签失败，可能数据被篡改');
        }

        // 回调商户的业务逻辑
        $flag = $this->callback($notify, $notifyData);
        if ($flag) {
            $msg = 'OK';
        } else {
            $msg = '商户逻辑调用出错';
        }
        // 返回响应值
        return $this->replyNotify($flag, $msg);
    }

    /**
     * 回调商户的业务逻辑，根据返回的true  或者 false  向第三方返回数据
     * @param PayNotifyInterface $notify
     * @param array $notifyData
     *
     * @return boolean
     * @author helei
     */
    protected function callback(PayNotifyInterface $notify, array $notifyData)
    {
        $data = $this->getRetData($notifyData);
        if ($data === false) {
            return false;
        }

        return $notify->notifyProcess($data);
    }

    /**
     * 获取移除通知的数据  并进行简单处理（如：格式化为数组）
     *
     * 如果获取数据失败，返回false
     *
     * @return array|false
     * @author helei
     */
    abstract protected function getNotifyData();

    /**
     * 检查异步通知的数据是否合法
     *
     * 如果检查失败，返回false
     *
     * @param array $data  由 $this->getNotifyData() 返回的数据
     * @return boolean
     * @author helei
     */
    abstract protected function checkNotifyData(array $data);

    /**
     * 向客户端返回必要的数据
     * @param array $data 回调机构返回的回调通知数据
     * @return array|false
     * @author helei
     */
    abstract protected function getRetData(array $data);

    /**
     * 根据返回结果，回答支付机构。是否回调通知成功
     * @param boolean $flag 每次返回的bool值
     * @param string $msg 通知信息，错误原因
     * @return mixed
     * @author helei
     */
    abstract protected function replyNotify($flag, $msg = 'OK');
}