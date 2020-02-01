<h1 align="center">Payment使用文档</h1>


# JetBrains OS licenses

`payment` had been being developed with PhpStorm under the free JetBrains Open Source license(s) granted by JetBrains s.r.o., hence I would like to express by thanks here.

[![Stargazers over time](./jetbrains-variant-4.svg)](https://www.jetbrains.com/?from=ABC)


[![Software license][ico-license]](LICENSE)
[![Latest development][ico-version-dev]][link-packagist]
[![Monthly installs][ico-downloads-monthly]][link-downloads]

老版本文档：http://helei112g.github.io/payment

新版本文档如下


## Stargazers over time

[![Stargazers over time][starchart-cc]](https://starchart.cc/helei112g/payment)

-----

# 联系&打赏

[打赏名单](SUPPORT.md)

请大家使用时根据示例代码来，有bug直接提交 `issue`；未避免不必要的时间成本，提供付费技术支持。

<div style="margin:0 auto;">
    <p align="center" style="margin:0px;"><img width="60%" src="https://dayutalk.cn/img/pub-qr.jpeg?v=123"></p>
    <p align="center" style="margin:0px;"><img width="60%" src="https://dayutalk.cn/img/pay-qr.jpeg"></p>
</div>


# 目录

- [公告](#公告)
    - [重要通知](#重要通知)
    - [计划](#计划)
- [Payment解决什么问题](#Payment解决什么问题)
- [如何使用](#如何使用)
    - [安装](#安装)
    - [项目集成](#项目集成)
    - [设计支付系统](#设计支付系统)
    - [支持的接口](#支持的接口)
- [贡献指南](#贡献指南)
    - [代码设计](#代码设计)
    - [接入支付指南](#接入支付指南)
- [第三方文档](#第三方文档)
- [License](#License)

# 公告

第三方支付的一些重要更新提示，以及项目相关的计划信息。

## 重要通知

1. 2019-04: **提醒：微信CA证书进行了更新，请更新项目到最新版本。否则5月29日后，将无法支付**
> 官方公告： https://pay.weixin.qq.com/index.php/public/cms/content_detail?lang=zh&id=56602

## 计划

重构整个项目，doing... ...

**重构后的项目与 `4.x` 以前的版本不兼容，请使用者注意！**

# Payment解决什么问题

`Payment` 的目的是简化大家在对接主流第三方时需要频繁去阅读第三方文档，还经常遇到各种问题。`Payment` 将所有第三方的接口进行了合理的建模分类，对大家提供统一的接入入口，大家只需要关注自身业务并且支付系统设计上。

目前已经集成：支付宝、微信、招商绝大部分功能。也欢迎各位贡献代码。 [贡献指南](#贡献指南)


# 如何使用

## 安装

当前 `Payment` 项目仅支持 `PHP version > 7.0` 的版本，并且仅支持通过 `composer` 进行安装。

**需要 `PHP` 安装以下扩展：**

```txt
- ext-curl
- ext-mbstring
- ext-bcmath
- package-Guzzle
```

**composer安装方式：**

直接在命令行下安装：

```bash
composer require "riverslei/payment:*"
```

通过项目配置文件方式安装：

```yaml
"require": {
    "riverslei/payment": "*"
}
```


## 项目集成

按照上面的步骤完成安装后，即可在项目中使用。

对于整个过程，提供了唯一的入口类 `\Payment\Client`，每一个渠道，均只介绍 `APP支付` 与 `异步/同步通知` 该如何接入。会重点说明每个请求支持的参数。

**APP支付demo**

```php
$config = [
    // 配置信息，各个渠道的配置模板见对应子目录
];

// 请求参数，完整参数见具体表格
$payData = [
    'body'         => 'test body',
    'subject'      => 'test subject',
    'trade_no'     => 'trade no',// 自己实现生成
    'time_expire'  => time() + 600, // 表示必须 600s 内付款
    'amount'       => '5.52', // 微信沙箱模式，需要金额固定为3.01
    'return_param' => '123',
    'client_ip'    => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1', // 客户地址
];``

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::WECHAT, $wxConfig);
    $res    = $client->pay(\Payment\Client::WX_CHANNEL_APP, $payData);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\GatewayException $e) {
    echo $e->getMessage();
    var_dump($e->getRaw());
    exit;
} catch (\Payment\Exceptions\ClassNotFoundException $e) {
    echo $e->getMessage();
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

```


**异步/同步通知**

```php
// 自己实现一个类，继承该接口
class TestNotify implements \Payment\Contracts\IPayNotify
{
    /**
     * 处理自己的业务逻辑，如更新交易状态、保存通知数据等等
     * @param string $channel 通知的渠道，如：支付宝、微信、招商
     * @param string $notifyType 通知的类型，如：支付、退款
     * @param string $notifyWay 通知的方式，如：异步 async，同步 sync
     * @param array $notifyData 通知的数据
     * @return bool
     */
    public function handle(
        string $channel,
        string $notifyType,
        string $notifyWay,
        array $notifyData
    ) {
        //var_dump($channel, $notifyType, $notifyWay, $notifyData);exit;
        return true;
    }
}

$config = [
    // 配置信息，各个渠道的配置模板见对应子目录
];

// 实例化继承了接口的类
$callback = new TestNotify();

try {
    $client = new \Payment\Client(\Payment\Client::ALIPAY, $config);
    $xml = $client->notify($callback);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\GatewayException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\ClassNotFoundException $e) {
    echo $e->getMessage();
    exit;
} catch (Exception $e) {
     echo $e->getMessage();
     exit;
 }

```

从上面的例子简单总结下，所有的支持的能力，通过 `\Payment\Client` 对外暴露方法；所有需要的常量也在这个类中进行了定义。其次需要一个 `$config`，关于config的模板，在每个渠道下面去看。最后一个传入请求的参数，完整的参数会在每个渠道中列出来，需要说明的是这些参数名字根据第三方文档部分进行了改写。在使用的时候请注意。

### 支付宝

**配置文件模板**

```php
$config = [
    'use_sandbox' => true, // 是否使用沙盒模式

    'app_id'    => '2016073100130857',
    'sign_type' => 'RSA2', // RSA  RSA2


    // 支付宝公钥字符串
    'ali_public_key' => '',

    // 自己生成的密钥字符串
    'rsa_private_key' => '',

    'limit_pay' => [
        //'balance',// 余额
        //'moneyFund',// 余额宝
        //'debitCardExpress',// 	借记卡快捷
        //'creditCard',//信用卡
        //'creditCardExpress',// 信用卡快捷
        //'creditCardCartoon',//信用卡卡通
        //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
    ], // 用户不可用指定渠道支付当有多个渠道时用“,”分隔

    // 与业务相关参数
    'notify_url' => 'https://dayutalk.cn/notify/ali',
    'return_url' => 'https://dayutalk.cn',

    'return_raw' => false, // 在处理回调时，是否直接返回原始数据，默认为 true
];


```

### 微信

**配置文件模板**

```php

$config = [
    'use_sandbox' => false, // 是否使用 微信支付仿真测试系统

    'app_id'       => 'wxxxxxxxx',  // 公众账号ID
    'mch_id'       => '123123123', // 商户id
    'md5_key'      => '23423423dsaddasdas', // md5 秘钥
    'app_cert_pem' => 'apiclient_cert.pem',
    'app_key_pem'  => 'apiclient_key.pem',
    'sign_type'    => 'MD5', // MD5  HMAC-SHA256
    'limit_pay'    => [
        //'no_credit',
    ], // 指定不能使用信用卡支付   不传入，则均可使用
    'fee_type' => 'CNY', // 货币类型  当前仅支持该字段

    'notify_url' => 'https://dayutalk.cn/v1/notify/wx',

    'redirect_url' => 'https://dayutalk.cn/', // 如果是h5支付，可以设置该值，返回到指定页面

    'return_raw' => false, // 在处理回调时，是否直接返回原始数据，默认为true
];
```



使用的几个注意点：

1. \Payment\Client 定义了支持的所有支付渠道，如：\Payment\Client::WECHAT


### 招商银行

**配置文件模板**

```php

$config = [
    'use_sandbox' => true, // 是否使用 招商测试系统

    'branch_no' => 'xxx',  // 商户分行号，4位数字
    'mch_id'    => 'xxxx', // 商户号，6位数字
    'mer_key'   => 'xxxxxx', // 秘钥16位，包含大小写字母 数字

    // 招商的公钥，建议每天凌晨2:15发起查询招行公钥请求更新公钥。
    'cmb_pub_key' => 'xxxxx',

    'op_pwd'    => 'xxxxx', // 操作员登录密码。
    'sign_type' => 'SHA-256', // 签名算法,固定为“SHA-256”
    'limit_pay' => [
        //'A',
    ], // 允许支付的卡类型,默认对支付卡种不做限制，储蓄卡和信用卡均可支付   A:储蓄卡支付，即禁止信用卡支付

    'notify_url' => 'https://dayutalk.cn/notify/cmb', // 支付成功的回调

    'sign_notify_url' => 'https://dayutalk.cn/notify/cmb', // 成功签约结果通知地址
    'sign_return_url' => 'https://dayutalk.cn', // 成功签约结果通知地址

    'return_url' => 'https://dayutalk.cn', // 如果是h5支付，可以设置该值，返回到指定页面

    'return_raw' => false, // 在处理回调时，是否直接返回原始数据，默认为true
];
```

## 设计支付系统

`Payment` 解决了对接第三方渠道的各种问题，但是一个合理的支付完整系统该如何设计？估计大家还有很多疑问。关于支付系统的设计大家可以参考该项目：https://github.com/skr-shop/manuals

这是我与小伙伴开源的另外一个关于电商的项目，里边对电商的各个模块设计进行了详细的描述。

## 支持的接口

对应到第三方的具体接口

### 支付宝

### 微信

支持 `普通商户与服务商两个版本`

- [付款码支付](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1)
- [JSAPI支付](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1)
- [Native支付](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1)
- [APP支付](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1)
- [H5支付](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1)
- [小程序支付](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1)
- [退款](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_4&index=4)
- [关闭交易](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_3&index=3)
- [交易查询](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_2&index=2)
- [退款查询](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_5&index=5)
- [企业转账查询](https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_3)
- [零钱转账查询](https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_3)
- [下载对账单](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_6&index=6)
- [下载资金账单](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_18&index=7)
- [企业转账](https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_2)
- [零钱转账](https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2)


### 招商

# 贡献指南

## 代码设计

整个代码结构的设计

## 开发指南

接入一个新的能力该如何操作

#第三方文档

- [支付宝](https://docs.open.alipay.com/api_1)
- [微信](https://pay.weixin.qq.com/wiki/doc/api/index.html)
- [招商银行](http://openhome.cmbchina.com/paynew/pay/Home)

# License

The code for Payment is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).


[ico-license]: https://img.shields.io/github/license/helei112g/payment.svg
[ico-version-dev]: https://img.shields.io/packagist/vpre/riverslei/payment.svg
[ico-downloads-monthly]: https://img.shields.io/packagist/dm/riverslei/payment.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/riverslei/payment
[link-downloads]: https://packagist.org/packages/riverslei/payment/stats
[starchart-cc]: https://starchart.cc/helei112g/payment.svg
