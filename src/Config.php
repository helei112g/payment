<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:47
 * @description: 支付相关的基础配置  无法被继承
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 *
 * @version 2.6.1
 */

namespace Payment;

final class Config
{
    const VERSION = '3.1.1-dev';

    //========================= ali相关接口 =======================//
    // 支付相关常量
    const ALI_CHANNEL_APP = 'ali_app';// 支付宝 手机app 支付

    const ALI_CHANNEL_WAP = 'ali_wap';// 支付宝 手机网页 支付

    const ALI_CHANNEL_WEB = 'ali_web';// 支付宝 PC 网页支付

    const ALI_CHANNEL_QR = 'ali_qr';// 支付宝 扫码支付

    const ALI_CHANNEL_BAR = 'ali_bar';// 支付宝 条码支付

    // 其他操作常量
    const ALI_CHARGE = 'ali_charge';// 支付

    const ALI_REFUND = 'ali_refund';// 退款

    const ALI_RED = 'ali_red';// 红包

    const ALI_TRANSFER = 'ali_transfer';// 转账


    //========================= 微信相关接口 =======================//
    // 支付常量
    const WX_CHANNEL_APP = 'wx_app';// 微信 APP 支付

    const WX_CHANNEL_PUB = 'wx_pub';// 微信 公众账号 支付

    const WX_CHANNEL_QR = 'wx_qr';// 微信 扫码支付  (可以使用app的帐号，也可以用公众的帐号完成)

    const WX_CHANNEL_BAR = 'wx_bar';// 微信 刷卡支付，与支付宝的条码支付对应

    const WX_CHANNEL_LITE = 'wx_lite';// 微信小程序支付

    const WX_CHANNEL_WAP = 'wx_wap';// 微信wap支付，针对特定用户

    // 其他相关常量
    const WX_CHARGE = 'wx_charge';// 支付

    const WX_REFUND = 'wx_refund';// 退款

    const WX_RED = 'wx_red';// 红包

    const WX_TRANSFER = 'wx_transfer';// 转账



    //========================= 招商相关接口 =======================//
    // 支付常量
    const CMB_CHANNEL_APP = 'cmb_app';// 招商 app  ，实际上招商并无该概念

    const CMB_CHANNEL_WAP = 'cmb_wap';// 招商h5支付，其实app支付也是使用的h5

    const CMB_BIND = 'cmb_bind';// 签约API

    const CMB_PUB_KEY = 'cmb_pub_key';// 查询招商公钥

    const CMB_CHARGE = 'cmb_charge';// 招商支付

    const CMB_REFUND = 'cmb_refund';// 招商退款


    //========================= 金额问题设置 =======================//
    const PAY_MIN_FEE = '0.01';// 支付的最小金额

    const TRANS_FEE = '50000';// 转账达到这个金额，需要添加额外信息


    //======================= 交易状态常量定义 ======================//
    const TRADE_STATUS_SUCC = 'success';// 交易成功

    const TRADE_STATUS_FAILD  = 'not_pay';// 交易未完成


    //======================= 账户类型 ======================//
    const WECHAT_PAY = 'wechat';
    const ALI_PAY = 'ali';
    const CMB_PAY = 'cmb';
}
