<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/27
 * Time: 下午1:00
 */

namespace Payment\Common\Cmb\Data;


use Payment\Common\BaseData;
use Payment\Utils\ArrayUtil;

/**
 * Class BaseData
 *
 * @property string $version   调用的接口版本，固定为：1.0
 * @property string $charset  参数编码,固定为“UTF-8”
 * @property string $dateTime  求时间,格式为yyyyMMddHHmmss
 * @property string $branchNo  商户分行号，4位数字
 * @property string $merchantNo   商户号，6位数字
 * @property string $notifyUrl  服务器主动通知商户服务器里指定的页面http/https路径
 * @property string $signNoticeUrl  成功签约结果通知地址  商户接收成功签约结果通知的地址。
 * @property string $returnUrl  	HTTP/HTTPS开头字符串
 * @property string $merKey  用于加密的 key
 * @property string $client_ip  用户端实际ip
 *
 * @package Payment\Common\Weixin\Dataa
 */
abstract class CmbBaseData extends BaseData
{

    /**
     * 请求数据签名算法的实现
     * @param string $signStr
     * @return string
     */
    protected function makeSign($signStr)
    {
        switch ($this->signType) {
            case 'SHA-256':
                $sign = hash('sha256', "$signStr&{$this->merKey}");

                break;
            default:
                $sign = '';
        }

        return $sign;
    }

    /**
     * 构建数据
     */
    protected function buildData()
    {
        $signData = [
            // 公共参数
            'version'       => $this->version,
            'charset'       => $this->charset,
            'signType'      => $this->signType,
            'reqData'       => $this->getReqData(),
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 请求数据
     *
     * @return array
     */
    abstract protected function getReqData();
}