<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/27
 * Time: 下午1:00
 */

namespace Payment\Common\Cmb\Data;

use Payment\Common\BaseData;
use Payment\Common\PayException;
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
 * @property string $opPwd  用于加密的 key
 * @property string $client_ip  用户端实际ip
 * @property string $serial_no  协议开通请求流水号，开通协议时必填。
 * @property string $agr_no 客户协议号。必须为纯数字串，不超过30位。
 * @property string $user_id 用于标识商户用户的唯一ID。 商户系统内用户唯一标识，不超过20位，数字字母都可以，建议纯数字
 * @property string $mobile 商户用户的手机号
 * @property string $risk_level 风险等级:用户在商户系统内风险等级标识
 * @property string $return_param  结果通知附加参数  该参数在发送成功签约结果通知时，将原样返回商户 注意：该参数可为空，商户如果需要不止一个参数，可以自行把参数组合、拼装，但组合后的结果不能带有’&’字符。
 *
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
     * 检查基本数据
     */
    protected function checkDataParam()
    {
        $branchNo = $this->branchNo;
        $merchantNo = $this->merchantNo;

        if (empty($branchNo) || mb_strlen($branchNo) !== 4) {
            throw new PayException('商户分行号，4位数字');
        }

        if (empty($merchantNo) || mb_strlen($merchantNo) !== 6) {
            throw new PayException('商户号，6位数字');
        }
    }

    /**
     * 请求数据
     *
     * @return array
     */
    abstract protected function getReqData();
}
