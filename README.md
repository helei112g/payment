[![Software license][ico-license]](LICENSE)
[![Latest development][ico-version-dev]][link-packagist]
[![Monthly installs][ico-downloads-monthly]][link-downloads]

-----

# [Payment3.x 使用教程](https://helei112g.github.io/categories/payment-3/)

## 重要变更 ##
- 即时到账支付宝异步通知自动识别处理，example演示demo优化 (from v3.0.3)
- 支付宝密钥公钥自动设置对应头信息（fromv3.0.2）
- 支付宝密钥支持字符串、文件两种方式配置，微信支持HMAC-SHA256加密（from v3.0.1）
- 支持支付宝rsa2签名 加入支付宝当面付-条码支付(条码与声波两种模式)   微信加入刷卡支付、小程序支付、H5支付  提供客户端静态调用类 不再兼容支付宝老版本接口（from v3.0.0）
- 支持支付宝新版本支付接口（from v2.7.0）
- 配置文件控制权限由使用者控制（from v2.0.0）

# Payment 能够做什么 #

Paymeng 主要帮助 php 开发者在服务端快速接入主流的支付平台(支付宝支付、微信支付等)。节省时间少走弯路。

Payment 针对不同支付平台，提供了统一的调用方式，开发者无需再一个平台一个平台的去阅读文档、调试。所有的支付平台后台服务统一用一套代码，在支付对接模块的代码维护量大大减少，可以把更多的时间和精力花在自身产品的核心业务上

### 为什么要用Payment SDK ###

所有的支付官方都提供了demo，方便开发者学习使用。但是其中每一个支付 demo 都不尽相同，并且不少 官方 demo 还有不少错误，让开发者使用的时候莫名其妙。

针对不同的支付，官方demo写法各异，很多还使用了老旧的 php 语法。

而 **Payment SDK** 针对不同服务商的支付功能，都提供统一的调用方式，大大降低学习与使用成本。

### 与其他聚合支付服务的对比 ###
这里最主要的对比对象是ping++。当然我这个个人开发者肯定没法与之相比。首先ping++服务更多，接入的支付种类更多。开放出来的接口也更多。

但是本sdk的优势也非常明显。
- 使用项目自己部署，只需向第三方支付服务提供者付费（阿里、腾讯）。
- 项目开源，遵循 **MIT** 许可证，大家可自由更改。
- 根据自己需求，可以自己动手定义个性化。
- 通过composer安装管理，方便升级。
- 就算我以后不维护升级了，也保证你代码可用，如果用第三方聚合的支付，与第三方就有了强关联。

### 其他 ###

开发者只需要专注自己的业务，对于主流支付方式本sdk会持续集成

*招商一网通支付 正在开发中... ...*

更多详细情况请[点击这里](https://helei112g.github.io/categories/payment-3/)

由于 `payment v1` 版本在设计开发时的缺陷，不在进行维护升级。并且 `v2` 版本也不与之兼容。建议大家都升级v2版本

## 安装与使用Payment ##

推荐大家通过composer来进行安装。
* 方式一

通过composer，这是推荐的方式，可以使用composer.json 声明依赖，或者运行下面的命令。SDK 包已经放到这里 riverslei/payment

```php
    composer require "riverslei/payment:~3.0"
```

放入composer.json文件中

```php
    "require": {
        "riverslei/payment": "~3.0"
    }
```

* 方式二
直接下载安装，SDK 没有依赖其他第三方库，但需要参照 composer的autoloader，增加一个自己的autoloader程序。

代码中以提供一个默认autolaod.php  可直接使用.


**Payment**需要 PHP >= 5.6，并且需要安装以下扩展：
- cURL extension
- mbstring
- BC Math

Payment SDK使用文档[请看这里](https://helei112g.github.io/categories/payment-3/)

## 联系&打赏 ##

如果真心觉得项目帮助到你，为你节省了成本，欢迎鼓励一下。

如果有什么问题，可通过以下方式联系我。提供有偿技术服务。

也希望更多朋友可用提供代码支持。欢迎交流与大赏。

**邮箱**：dayugog@gmail.com

**不错，我要鼓励一下**

![image](http://ol59nqr1i.bkt.clouddn.com/ali-wx-pay.jpg?imageView2/2/w/500)

**打赏名单**

名字 | 金额 | 说明 | 时间
---|---|---|---
李 | 100.00 | 打赏支持 | 2017-03-14
Alex.Ma | 6.66 | 支持 | 2017-03-13
阿笨 | 10.00 | 打算使用，先感谢一下 | 2017-03-10
彦 | 88.00 | 感觉还不错，特打赏88元，略表感谢。 | 2017-02-28
汤明洋 | 66.66 | 支持一下 | 2017-02-19
李仕建同学 | 18.88 | 新春快乐 | 2017-02-09
凡额 | 50.00 | 帮助调试，谢谢了 | 2017-01-18
Thans秦 | 66.66 | 商业使用 | 2017-01-08
John | 10.00 | 设计很棒 | 2017-01-06
Davidw | 699.00 | 支持开发2.0 | 2016-12-15
宁静致远 | 10.00 | 鼓励你，加油额 | 2016-12-13
k7 | 8.00 | 批量付款，一次成功 | 2016-11-24
洋 | 50.00 | 资助开源 | 2016-11-23
张仲东 | 50.00 | 接口封装的不错 | 2016-11-17
放下...快乐 | 1000.00 | 支付宝即时到帐处理 | 2016-11-15
Robin Core Animation | 50.00 | 解决微信支付问题 | 2016-11-04
5Z4 | 20.00 | 解决回调问题 | 2016-10-31
哈罗Joe | 1.00 | 加油~~ | 2016-8-23
小兵~招UI前端 | 50.00 | 继续努力,喝杯水吧:-) | 2016-8-14
尊称韦爵爷 | 1.00 | 赶紧出个yii的扩展 | 2016-7-22
一米市集 | 1000.00 | 希望提供技术长期合作 | 2016-7-20
张松 | 15.00 | 不错，已用到项目中 | 2016-6-17

### License ###

The code for Payment is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).


[ico-license]: https://img.shields.io/github/license/helei112g/payment.svg
[ico-version-dev]: https://img.shields.io/packagist/vpre/riverslei/payment.svg
[ico-downloads-monthly]: https://img.shields.io/packagist/dm/riverslei/payment.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/riverslei/payment
[link-downloads]: https://packagist.org/packages/riverslei/payment/stats