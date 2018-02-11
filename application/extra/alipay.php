<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:19
 * @description:
 */

// 一下配置均为本人的沙箱环境，贡献出来，大家测试

// 个人沙箱帐号：
/*
 * 商家账号   naacvg9185@sandbox.com
 * 商户UID   2088102169252684
 * appId     2016073100130857
 */

/*
 * 买家账号    aaqlmq0729@sandbox.com
 * 登录密码    111111
 * 支付密码    111111
 */

return [
    'use_sandbox'               => false,// 是否使用沙盒模式

    'partner'                   => '2088821948262947',
    'app_id'                    => '2017121500795454',
    'sign_type'                 => 'RSA2',// RSA  RSA2

    // 可以填写文件路径，或者密钥字符串  当前字符串是 rsa2 的支付宝公钥(开放平台获取)
    'ali_public_key'            => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0qznDrC8ygcwECLI81F/+gctzPtzLFf22xPW5l22hWWLKr1HUVS6eEh4oa2AMNr7Xk3mrYb6gf7LnuSoWOLxqGtzlsCtSn4vfsJfA2OIJzVVD6ssnCYH8UuQHURMlyJ4zzOVqWmSFP1814+1CSrt6e9LfMcjkCd8ZtYnyJj6CEHZSItJdsh7F9DKMbW3a/jBOgosQ8ssqqCUUkxe9p1ktA/qTRjVcvqJZ+C7AexjX89IzKanWIt3zlC0Nt5eoauNFdhh3T8i7YeM00y7qmSuqOKC8+Xvw8cnMI6lxJChagj5DmXfWLXnXzBZHwFphfJaBv1VuR3R8PRAFWYoyAyipwIDAQAB',
    // 可以填写文件路径，或者密钥字符串  我的沙箱模式，rsa与rsa2的私钥相同，为了方便测试
    'rsa_private_key'           => 'MIIEowIBAAKCAQEA0qznDrC8ygcwECLI81F/+gctzPtzLFf22xPW5l22hWWLKr1HUVS6eEh4oa2AMNr7Xk3mrYb6gf7LnuSoWOLxqGtzlsCtSn4vfsJfA2OIJzVVD6ssnCYH8UuQHURMlyJ4zzOVqWmSFP1814+1CSrt6e9LfMcjkCd8ZtYnyJj6CEHZSItJdsh7F9DKMbW3a/jBOgosQ8ssqqCUUkxe9p1ktA/qTRjVcvqJZ+C7AexjX89IzKanWIt3zlC0Nt5eoauNFdhh3T8i7YeM00y7qmSuqOKC8+Xvw8cnMI6lxJChagj5DmXfWLXnXzBZHwFphfJaBv1VuR3R8PRAFWYoyAyipwIDAQABAoIBAGev31x0kcYg+X/Hjv1qLEFjTSBo1UuK050JS6g/kThgPLlBRRt7RvcyO+Te3outCzBjyNe46gwW2iKOyWhN8cABendEx0U1i6yof1jMoNMjRYUbqy4C7b/Nf9VXqxZrSeg4rD0gD1yzUcFKIkCr10JemtmDrTYiqPB4EEaDO/PZiDtQkhoaTJmZHzwh/wryB92V7NVB4j4PKizfTchIMPsfatOObkA/ayYPwnQXsZsh7eAxGeUzmsorRfAF/PBvTGKytsq1ddcJVoJzrkuBoxiApoKE0cDGdzRkVeYcskpZ/ejEoZegXbWyaHSYQrVw0HG0CX+sLgvSydK+BEpGGuECgYEA6gQVMV3V6tUuUXaxNZeGx7rQTKT6QSuYYx4srQVkFyC9NTxlw7FdrUTTZVf7eNzkyJEoBfZ91p9+d/8EZPpsRJvZUydXNgJQ3WG1SDzJZrjmEsQy5RS8j3W7hJj51G6xwDgd/0CipJi2MruDdLiGrKF2wRXGLkJStsEZNdy9adcCgYEA5nd/BxFZTqHQfX+d+pBxSR0U8stLwMn7xPIsa6JE65wQ86GA5xU6o7Hh9Mfy4wM1mzluaDA46q/dSk+93xf0hTNf0Yfc45bUIVyS1W4BAnEuvcRJ5sHdmXH8Ht98mzG0ilFR6ZmosP4Uw8Ap73SWm6+CIGH+YH9cawXGjjswk7ECgYAyiz00k2rftLmzWKp/w8GVpBDXbQ6sQslAJ0VI/ZAXureDJw9nzAhKo0HxcqZa/YLgmxGE8C9PytUa/9aoJLp7uxmebzHT9X4XwsHP32k0qZzv0raXafosFiaxRgU3z8zOkpBQEFrQqDc6D+wdqrlT0e//Vj+ewC5zUJOsYYf7swKBgQDcSGFpLwLurqnqGn20jjVJzftE6l3Ywvbb0yH0KyC3Fu/4/miH93maFx9DaY5Vv66QVH+cJGNypm/cZIW2ZF46ptUyICtYecT+sk5CpCdg3y/vAFwmrSyTSyjRlYmziPnorzudDVr4+ZJ9XAJ5NzXxTvsQ2rlaIMIEJXwYXKjcsQKBgH5kBtmz1K3PWM6spzaaSp7mrAkAkw8LiGoZHOxJHIp8eWeSkhyobvb9ta9lWrsRjPMuFxcu1okjACxeL41kP3sEF3hd9fJnUPKw4+GZXD28FgrE30G8HYvM+7FEMOvTUHXLgs/r3cvvkosSbd0EPh5zL3ewRRUCfCkPrYwfg2ID',

    'limit_pay'                 => [
        //'balance',// 余额
        //'moneyFund',// 余额宝
        //'debitCardExpress',// 	借记卡快捷
        //'creditCard',//信用卡
        //'creditCardExpress',// 信用卡快捷
        //'creditCardCartoon',//信用卡卡通
        //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
    ],// 用户不可用指定渠道支付当有多个渠道时用“,”分隔

    // 与业务相关参数
    'notify_url'                => 'http://api.greatsir.com/pay/callback/alipay',
    'return_url'                => 'http://www.greatsir.com/pay/success',

    'return_raw'                => false,// 在处理回调时，是否直接返回原始数据，默认为 true
];
