<h1 align="center">Payment使用文档</h1>

<p align="center">

[![Software license][ico-license]](LICENSE)
[![Latest development][ico-version-dev]][link-packagist]
[![Monthly installs][ico-downloads-monthly]][link-downloads]

</p>

## Stargazers over time

[![Stargazers over time][starchart-cc]](https://starchart.cc/helei112g/payment)

-----

# 联系&打赏

[赞赏名单](SUPPORT.md)

<div style="margin:0 auto;">
    <p align="center" style="margin:0px;">联系邮箱：dayugog@gmail.com</p>
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
- [License](#License)

# 公告

第三方支付的一些重要更新提示，以及项目相关的计划信息。

## 重要通知

1. 2019-04: **提醒：微信CA证书进行了更新，请更新项目到最新版本。否则5月29日后，将无法支付**
> 官方公告： https://pay.weixin.qq.com/index.php/public/cms/content_detail?lang=zh&id=56602

## 计划

重构整个项目，doing... ...

# Payment解决什么问题

`Payment` 的目的是简化大家在对接主流第三方时需要频繁去阅读第三方文档，还经常遇到各种问题。`Payment` 将所有第三方的接口进行了合理的建模分类，对大家提供统一的接入入口，大家只需要关注自身业务并且支付系统设计上。

目前已经集成：支付宝、微信、招商、建行绝大部分功能。也欢迎各位贡献代码。 [贡献指南](#贡献指南)


# License

The code for Payment is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).


[ico-license]: https://img.shields.io/github/license/helei112g/payment.svg
[ico-version-dev]: https://img.shields.io/packagist/vpre/riverslei/payment.svg
[ico-downloads-monthly]: https://img.shields.io/packagist/dm/riverslei/payment.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/riverslei/payment
[link-downloads]: https://packagist.org/packages/riverslei/payment/stats
[starchart-cc]: https://starchart.cc/helei112g/payment.svg
