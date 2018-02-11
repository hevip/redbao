<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2017/10/25
 * Time: 下午7:24
 */

namespace app\commpont\service;

use app\common\service\BaseService;
class QiniuService extends BaseService
{
    private $accessKey = 'moKuh5lFsIVKDD0qbb6Wv9r5JKMa3HPsIlh6mXi0';
    private $secretKey = 'ubZRdsRH81GxIklOr2umPy4D7dZpx7MBZuEnqVEy';
    private $scope = 'vgirlup';
    private $scopes = [
        'vgirlup','vgirlup1'
    ];
    public static function getToken($index)
    {

        //策略
        $policy = array(
            'returnBody' => '{
                  "name": $(fname),
                  "size": $(fsize),
                  "key": $(key),
                  "w": $(imageInfo.width),
                  "h": $(imageInfo.height),
                  "hash": $(etag)
            }'
        );
        $_self = new self();
        $token = $_self->uploadToken($_self->scopes[$index-1], null, 3600, $policy);
        if($token){
            return $token;
        }else{
            self::setError([
                'status_code'=>500,
                'message'    => 'token 获取失败'
            ]);
            return false;
        }
    }
    public function sign($data)
    {
        $hmac = hash_hmac('sha1', $data, $this->secretKey, true);
        return $this->accessKey . ':' . self::base64_urlSafeEncode($hmac);
    }
    public function signWithData($data)
    {
        $data = self::base64_urlSafeEncode($data);
        return $this->sign($data) . ':' . $data;
    }
    public function signRequest($urlString, $body, $contentType = null)
    {
        $url = parse_url($urlString);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";
        if ($body != null &&
            ($contentType == 'application/x-www-form-urlencoded') ||  $contentType == 'application/json') {
            $data .= $body;
        }
        return $this->sign($data);
    }
    public function verifyCallback($contentType, $originAuthorization, $url, $body)
    {
        $authorization = 'QBox ' . $this->signRequest($url, $body, $contentType);
        return $originAuthorization === $authorization;
    }
    public function privateDownloadUrl($baseUrl, $expires = 3600*24*365)
    {
        $deadline = time() + $expires;
        $pos = strpos($baseUrl, '?');
        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }
        $baseUrl .= $deadline;
        $token = $this->sign($baseUrl);
        return "$baseUrl&token=$token";
    }
    public function uploadToken(
        $bucket,
        $key = null,
        $expires = 3600,
        $policy = null,
        $strictPolicy = true
    ) {
        $deadline = time() + $expires;
        $scope = $bucket;
        if ($key != null) {
            $scope .= ':' . $key;
        }
        $args = array();
        $args = self::copyPolicy($args, $policy, $strictPolicy);
        $args['scope'] = $scope;
        $args['deadline'] = $deadline;
        $b = json_encode($args);
        return $this->signWithData($b);
    }
    /**
     *上传策略，参数规格详见
     *http://developer.qiniu.com/docs/v6/api/reference/security/put-policy.html
     */
    private static $policyFields = array(
        'callbackUrl',
        'callbackBody',
        'callbackHost',
        'callbackBodyType',
        'callbackFetchKey',
        'returnUrl',
        'returnBody',
        'endUser',
        'saveKey',
        'insertOnly',
        'detectMime',
        'mimeLimit',
        'fsizeLimit',
        'persistentOps',
        'persistentNotifyUrl',
        'persistentPipeline',
    );
    private static $deprecatedPolicyFields = array(
        'asyncOps',
    );
    private static function copyPolicy(&$policy, $originPolicy, $strictPolicy)
    {
        if ($originPolicy == null) {
            return;
        }
        foreach ($originPolicy as $key => $value) {
            if (in_array($key, self::$deprecatedPolicyFields)) {
                $message = $key.'has deprecated';
                self::setError([
                    'status_code'=>500,
                    'message'    =>$message
                ]);
                return false;
            }
            if (!$strictPolicy || in_array($key, self::$policyFields)) {
                $policy[$key] = $value;
            }
        }
        return $policy;
    }
    public function authorization($url, $body = null, $contentType = null)
    {
        $authorization = 'QBox ' . $this->signRequest($url, $body, $contentType);
        return array('Authorization' => $authorization);
    }

    public static function base64_urlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }
    /*
     * 获取下载地址
     */
    public static function getDownloadUrl($url)
    {
        $self = new self();
        $url = $self->privateDownloadUrl($url);
        return $url;
    }
}



