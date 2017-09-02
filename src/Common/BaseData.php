<?php
namespace Payment\Common;

use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 * @author: helei
 * @createTime: 2016-07-28 18:05
 * @description: 支付相关接口的数据基类
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class BaseData
 * 支付相关接口的数据基类
 * @package Payment\Common\Weixin\Dataa
 *
 * @property string $limitPay   用户不可用指定渠道支付
 * @property boolean $returnRaw  是否返回原始数据，只进行签名检查
 * @property string $useSandbox 是否使用的测试模式
 * @property string $signType  签名算法
 *
 */
abstract class BaseData
{

    /**
     * 支付的请求数据
     * @var array $data
     */
    protected $data;

    /**
     * 支付返回的数据
     * @var array $retData
     */
    protected $retData;

    /**
     * 配置类型
     * @var string $configType
     */
    protected $channel;

    /**
     * BaseData constructor.
     * @param ConfigInterface $config
     * @param array $reqData
     * @throws PayException
     */
    public function __construct(ConfigInterface $config, array $reqData)
    {
        if ($config instanceof WxConfig) {
            $this->channel = Config::WECHAT_PAY;
        } elseif ($config instanceof AliConfig) {
            $this->channel = Config::ALI_PAY;
        } elseif ($config instanceof CmbConfig) {
            $this->channel = Config::CMB_PAY;
        }

        $this->data = array_merge($config->toArray(), $reqData);

        try {
            $this->checkDataParam();
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 获取变量，通过魔术方法
     * @param string $name
     * @return null|string
     * @author helei
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * 设置变量
     * @param $name
     * @param $value
     * @author helei
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 设置签名
     * @author helei
     */
    public function setSign()
    {
        $this->buildData();

        if ($this->channel === Config::CMB_PAY) {
            $data = $this->retData['reqData'];
        } else {
            $data = $this->retData;
        }
        $values = ArrayUtil::removeKeys($data, ['sign']);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $this->retData['sign'] = $this->makeSign($signStr);
    }

    /**
     * 返回处理之后的数据
     * @return array
     * @author helei
     */
    public function getData()
    {
        return $this->retData;
    }

    /**
     * 签名算法实现  便于后期扩展微信不同的加密方式
     * @param string $signStr
     * @return string
     */
    abstract protected function makeSign($signStr);

    /**
     * 构建用于支付的签名相关数据
     * @return array
     */
    abstract protected function buildData();

    /**
     * 检查传入的参数. $reqData是否正确.
     * @return mixed
     * @throws PayException
     */
    abstract protected function checkDataParam();
}
