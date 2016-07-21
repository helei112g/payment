<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:28
 * @description: 支付宝相关数据的基类
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common\Ali\Data;

use Payment\Common\AliConfig;
use Payment\Utils\ArrayUtil;

/**
 * Class BaseData
 *
 * @property string $getewayUrl
 * @property string $inputCharset
 * @property string $partner
 * @property string $md5Key
 * @property string $notifyUrl
 * @property string $returnUrl
 * @property integer $timeExpire
 * @property string $rsaPrivatePath
 * @property string $rsaAliPubPath
 * @property string $cacertPath
 * @property string $signType
 *
 * @package Payment\Charge\Ali\Data
 * anthor helei
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

    public function __construct(AliConfig $config, array $reqData)
    {
        $this->data = array_merge($reqData, $config->toArray());
    }

    /**
     * 获取变量，通过魔术方法
     * @param string $name
     * @return null|string
     * @author helei
     */
    protected function __get($name)
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
    protected function __set($name, $value)
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

        $values = ArrayUtil::removeKeys($this->retData, ['sign', 'sign_type']);

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
     * 实际执行的签名操作的算法
     * @param string $signStr
     * @return string
     * @author helei
     */
    abstract protected function makeSign($signStr);

    /**
     * 设置支付相关参数，
     * @return mixed
     * @author helei
     */
    abstract protected function buildData();
}