<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 上午10:48
 */

namespace Payment\Common\Cmb\Data;

use Payment\Common\PayException;

/**
 * 签约绑卡操作
 * Class BindCardData
 * @package Payment\Common\Cmb\Data
 *
 * @property string $lon 经度，商户app获取的手机定位数据，如30.949505
 * @property string $lat 纬度，商户app获取的手机定位数据，如50.949506
 *
 */
class BindCardData extends CmbBaseData
{
    protected function checkDataParam()
    {
        parent::checkDataParam();
        $agrNo = $this->agr_no;
        if (empty($agrNo) || mb_strlen($agrNo) > 30 || ! is_numeric($agrNo)) {
            throw new PayException('客户协议号。必须为纯数字串，不超过30位');
        }
    }

    protected function getReqData()
    {
        $reqData = [
            'dateTime' => $this->dateTime,
            'merchantSerialNo' => $this->serial_no ? $this->serial_no : '',
            'agrNo' => $this->agr_no,
            'branchNo' => $this->branchNo,
            'merchantNo' => $this->merchantNo,
            'userID' => $this->user_id ? $this->user_id : '',
            'mobile' => $this->mobile ? $this->mobile : '',
            'lon' => $this->lon ? $this->lon : '',
            'lat' => $this->lat ? $this->lat : '',
            'riskLevel' => $this->risk_level ? $this->risk_level : '',
            'noticeUrl' => $this->signNoticeUrl ? $this->signNoticeUrl : '',
            'noticePara' => $this->return_param ? $this->return_param : '',
            'returnUrl' => $this->returnUrl ? $this->returnUrl : '',
        ];

        // 这里不能进行过滤空值，招商的空值也要加入签名中
        return $reqData;
    }
}
