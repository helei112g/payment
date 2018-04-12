<?php
/**
 * @author: jingzhou
 * @createTime: 2018-03-03 15:14
 * @description: 微信红包
 */

namespace Payment\Common\Weixin\Data;

use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
 * 微信红包
 * Class RedData
 *
 * @property string $mch_billno  商户系统内部的单号，商户系统内部唯一,接口根据商户订单号支持重入，如出现超时可再调用。
 * @property string $transaction_id  微信生成的订单号，在支付通知中有返回
 * @property string $out_trade_no  商户侧传给微信的订单号
 * @property string $re_openid 接受红包的用户用户在wxappid下的openid，服务商模式下可填入msgappid下的openid
 * @property int $total_amount 红包总金额，单位为分
 * 
 * @property string $msgappid 服务商模式下触达用户时的appid(可填服务商自己的appid或子商户的appid)，服务商模式下必填，服务商模式下填入的子商户appid必须在微信支付商户平台中先录入，否则会校验不过。
 * @property int 发放红包使用场景，红包金额大于200时必传 PRODUCT_1:商品促销 PRODUCT_2:抽奖 PRODUCT_3:虚拟物品兑奖 PRODUCT_4:企业内部福利 PRODUCT_5:渠道分润 PRODUCT_6:保险回馈 PRODUCT_7:彩票派奖 PRODUCT_8:税务刮奖
 *
 * @package Payment\Common\Weixin\Data
 * anthor IT
 */
class RedData extends WxBaseData
{
    protected function buildData()
    {
        $this->retData = [
            'act_name'     =>   $this->act_name,//活动名称
            'client_ip'    =>   $this->client_ip,
            'mch_billno'   =>   $this->mch_billno,
            'mch_id'       =>   $this->mchId,
            'nonce_str'    =>   $this->nonceStr,
            're_openid'    =>   $this->re_openid,
            'remark'       =>   $this->remark,//备注信息
            'scene_id'     =>   $this->scene_id,//场景ID 
            'send_name'    =>   $this->send_name,//红包发送者名称
            'total_amount' =>   $this->total_amount,//红包金额
            'total_num'    =>   $this->total_num ? $this->total_num : 1,//红包人数
            'wishing'      =>   $this->wishing,//祝福语
            'wxappid'      =>   $this->appId,

            // 服务商
            'msgappid'     =>   $this->sub_appid,
            'sub_mch_id'   =>   $this->sub_mch_id,
        ];
        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    /**
     * 检查参数
     * @author helei
     */
    protected function checkDataParam()
    {
        $mch_billno = $this->mch_billno;// 商户退款单号
        $sendName = $this->send_name;
        $reOpenid = $this->re_openid;
        $wishing = $this->wishing;
        $actName = $this->act_name;
        $remark = $this->remark;
        $total_amount = $this->total_amount;
        $sendListid = $this->send_listid;//红包订单的微信单号

        if (empty($mch_billno)) {
            throw new PayException('请设置单号 mch_billno');
        }
        if (empty($sendName)) {
            throw new PayException('请设置发送名称 send_name');
        }
        if (empty($reOpenid)) {
            throw new PayException('请设置接收者 openID');
        }
        if (empty($wishing)) {
            throw new PayException("请设置祝福语"); 
        }
        if (empty($actName)) {
            throw new PayException("请设置活动名称"); 
        }
        if (empty($remark)) {
            throw new PayException("请设置备注信息"); 
        }
        // 微信使用的单位位分.此处进行转化
        $this->total_amount = bcmul($total_amount, 100, 0);
        if (empty($total_amount) || $total_amount < 1) {
            throw new PayException('转账金额错误');
        }
        // 二者不能同时为空
        if (empty($sendListid) && empty($mch_billno)) {
            throw new PayException('必须提供微信交易号或商户网站唯一订单号。建议使用微信交易号');
        }

        // 该接口，微信配置文件，必须提供cert  key  两个pem文件
        $certPath = $this->appCertPem;
        $keyPath = $this->appKeyPem;
        if (empty($certPath)) {
            throw new PayException('红包接口，必须提供 apiclient_cert.pem 证书');
        }

        if (empty($keyPath)) {
            throw new PayException('红包接口，必须提供 apiclient_key.pem 证书');
        }

    }
}
