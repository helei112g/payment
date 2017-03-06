<?php
/**
 * 输入一个字符串，生成支付宝公钥 pem 文件
 * 由于支付宝现在提供的pem是字符串格式。大家可以通过该文件，生成对应的pem文件
 *
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/1
 * Time: 下午12:30
 */

// 从开发者平台拷贝的支付宝公钥key
$keyStr = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAxxxxxxxx';

$filename = 'alipay_public_key.pem';// 默认生成在当前目录

$keyStr = str_replace("-----BEGIN PUBLIC KEY-----", "", $keyStr);
$keyStr = str_replace("-----END PUBLIC KEY-----", "", $keyStr);
$keyStr = str_replace("\n", "", $keyStr);

$alipay_public_key = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . wordwrap($keyStr, 64, "\n", true) . PHP_EOL . '-----END PUBLIC KEY-----';

file_put_contents($filename, $alipay_public_key);


// 个人私钥生成
/*$keyStr = str_replace("-----BEGIN PRIVATE KEY-----", "", $keyStr);
$keyStr = str_replace("-----END PRIVATE KEY-----", "", $keyStr);
$keyStr = str_replace("\n", "", $keyStr);

$rsa_private_key = '-----BEGIN PRIVATE KEY-----' . PHP_EOL . wordwrap($keyStr, 64, "\n", true) . PHP_EOL . '-----END PRIVATE KEY-----';

file_put_contents($filename, $rsa_private_key);*/