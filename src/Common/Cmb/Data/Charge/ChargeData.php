<?php
namespace Payment\Common\Cmb\Data\Charge;

use Payment\Common\Cmb\Data\CmbBaseData;
use Payment\Common\CmbConfig;
use Payment\Common\PayException;
use Payment\Config;

/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/27
 * Time: 下午1:01
 *
 * @property string $date 订单日期,格式：yyyyMMdd
 * @property string $order_no  订单号, 10位数字，由商户生成，一天内不能重复。订单日期+订单号唯一定位一笔订单。
 * @property string $amount  金额, 格式：xxxx.xx  固定两位小数，最大11位整数
 * @property integer $timeout_express  过期时间
 * @property string $lon 经度，商户app获取的手机定位数据，如30.949505
 * @property string $lat 纬度，商户app获取的手机定位数据，如50.949506
 *
 */
class ChargeData extends CmbBaseData
{
    /**
     * 发送请求
     */
    protected function checkDataParam()
    {
        parent::checkDataParam();
        $amount = $this->amount;
      
        // 订单号交给支付系统自己检查

        // 检查金额不能低于0.01
        if (bccomp($amount, Config::PAY_MIN_FEE, 2) === -1) {
            throw new PayException('支付金额不能低于 ' . Config::PAY_MIN_FEE . ' 元');
        }

        // 设置ip地址
        $clientIp = $this->client_ip;
        if (empty($clientIp)) {
            $this->client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        }

        $timeExpire = $this->timeout_express;
        if (! empty($timeExpire)) {
            $express = floor(($timeExpire - strtotime($this->dateTime)) / 60);

            if ($express > CmbConfig::MAX_EXPIRE_TIME || $express < 0) {// 招商规定
                $this->timeout_express = CmbConfig::MAX_EXPIRE_TIME;
            } else {
                $this->timeout_express = $express;
            }
        }
    }

    /**
     * 请求数据
     */
    protected function getReqData()
    {
        $reqData = [
            'dateTime' => $this->dateTime,
            'branchNo' => $this->branchNo,
            'merchantNo' => $this->merchantNo,
            'date' => $this->date ? $this->date : date('Ymd', time()),
            'orderNo' => $this->order_no,
            'amount' => $this->amount,
            'expireTimeSpan' => $this->timeout_express ? $this->timeout_express  : '',
            'payNoticeUrl' => $this->notifyUrl,
            'payNoticePara' => $this->return_param ? $this->return_param : '',
            'returnUrl' => $this->returnUrl ? $this->returnUrl : '',
            'clientIP' => $this->client_ip,
            'cardType' => $this->limitPay ? $this->limitPay : '',
            'agrNo' => $this->agr_no,
            'merchantSerialNo' => $this->serial_no ? $this->serial_no : '',
            'userID' => $this->user_id ? $this->user_id : '',
            'mobile' => $this->mobile ? $this->mobile : '',
            'lon' => $this->lon ? $this->lon : '',
            'lat' => $this->lat ? $this->lat : '',
            'riskLevel' => $this->risk_level ? $this->risk_level : '',
            'signNoticeUrl' => $this->signNoticeUrl ? $this->signNoticeUrl : '',
            'signNoticePara' => $this->return_param ? $this->return_param : '',

            // 暂时先不支持下面方式
            'extendInfo' => '',
            'extendInfoEncrypType' => '',
        ];

        // 这里不能进行过滤空值，招商的空值也要加入签名中
        return $reqData;
    }
}
