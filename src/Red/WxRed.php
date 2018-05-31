<?php
namespace Payment\Red;

use Payment\Common\Weixin\Data\RedData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 * 微信红包接口
 * Class WxRed
 * @package Payment\Trans
 * anthor jingzhou
 */
class WxRed extends WxBaseStrategy
{
	protected $reqUrl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';

    public function getBuildDataClass()
    {
        return RedData::class;
    }

    /**
     * 普通红包的返回数据
     * @param array $ret
     * @return mixed
     */
    protected function retData(array $ret)
    {
        if ($this->config->returnRaw) {
            $ret['channel'] = Config::WX_RED;
            return $ret;
        }

        // 请求失败，可能是网络
        if ($ret['return_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $ret['return_msg'],
                'channel'   => Config::WX_RED,
            ];
        }

        // 业务失败
        if ($ret['result_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $ret['err_code_des'],
                'channel'   => Config::WX_RED,
            ];
        }

        return $this->createBackData($ret);
    }

    /**
     * 返回数据
     * @param array $data
     * @return array
     */
    protected function createBackData(array $data)
    {
        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'mch_billno'   => $data['mch_billno'],
                'send_listid'  => $data['send_listid'],
                'total_amount' => $data['total_amount'],
                're_openid'    => $data['re_openid'],
                'wxappid'      => $data['wxappid'],
                'mch_id'       => $data['mch_id'],
                'channel'   => Config::WX_RED,
            ],
        ];

        return $retData;
    }
    /**
     * 企业转账，不需要签名，使用返回true
     * @param array $retData
     * @return bool
     */
    protected function verifySign(array $retData)
    {
        return true;
    }
}