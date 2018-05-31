<?php
/**
 * Created by ChaXian.
 * User: Bing
 * Date: 2018/5/24
 * Time: 13:55
 */

namespace Payment\Common\Ali\Data\Charge;


use Payment\Common\ConfigInterface;
use Payment\Config;
use Payment\Utils\ArrayUtil;
use Payment\Utils\Md5Encrypt;

/**
 * Class DriectChargeData
 * @package Payment\Common\Ali\Data\Charge
 *
 * @property string $service
 * @property string $extend_param
 * @property string $seller_email
 * @property string $key
 */
class DirectChargeData extends ChargeBaseData
{
    public function __construct(ConfigInterface $config, array $reqData)
    {
        $this->channel = Config::ALI_PAY;
        $this->data = array_merge($config->toArray(), $reqData);

        try {
            $this->checkDataParam();
        } catch (PayException $e) {
            throw $e;
        }
    }

    protected function getBizContent()
    {
        $content = [
            'extend_param'      => $this->extend_param,
            'service'           => $this->service,
            'partner'           => $this->partner,
            '_input_charset'    => $this->charset,
            'notify_url'        => $this->notifyUrl,
            'return_url'        => $this->returnUrl,
            /* 业务参数 */
            'subject'           => $this->subject,
            'out_trade_no'      => $this->order_no,
            'price'             => $this->amount,
            'quantity'          => 1,
            'payment_type'      => $this->goods_type,
            /* 物流参数 */
            'logistics_type'    => 'EXPRESS',
            'logistics_fee'     => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
            /* 买卖双方信息 */
            'seller_email'      => $this->seller_email,
            'sign_type'         => $this->signType,
        ];

        return $content;
    }

    /**
     * 签名算法实现(此种支付方式只支持MD5)
     * @param string $signStr
     * @return string
     */
    protected function makeSign($signStr)
    {
        $rsa = new Md5Encrypt($this->key);
        return $rsa->encrypt($signStr);
    }

    public function setSign()
    {
        $this->buildData();
        $data = $this->retData;

        $values = ArrayUtil::removeKeys($data, ['sign_type']);
        $values = ArrayUtil::arraySort($values);
        $signStr = ArrayUtil::createLinkstring($values);

        $this->retData['sign'] = $this->makeSign($signStr);
    }

    protected function buildData()
    {
        $content = $this->getBizContent();

        $data = ArrayUtil::paraFilter($content);

        foreach ($data as $key => $value) {
            $data[$key] = urlencode($value);
        }

        $this->retData = $data;
    }

}