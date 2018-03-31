<?php
/**
 * @link http://www.yanphp.com/
 * @copyright Copyright (c) 2016 YANPHP Software LLC
 * @license http://www.yanphp.com/license/
 */
namespace yan\plugins\captcha;

class YanCaptcha{

    public $timeout=10;
    /*
     * 服务器验证接口
     * */
    public $verifyServer;

    /*
     * 构造函数
     * @param $config 初始化配置
     * */
    public function __construct($app_id,$secret,$verifyServer)
    {
        $this->app_id = $app_id;
        $this->secret = $secret;
        $this->verifyServer = $verifyServer;
    }

    public function verify($validate)
    {
        $params = [
            'app_id'=>$this->app_id,
            'secret'=>$this->secret,
            'validate'=>$validate,
            'timestamp'=>time(),
            'nonce'=>rand(1,10000)
        ];
        $sign = $this->generateSign($params);
        $params['sign'] = $sign;

        $result = $this->curl_http_post($params);
        return $result;
    }

    /*
     * 发送请求
     * @param $params 请求参数
     * */
    public function curl_http_post($params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->verifyServer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $result = curl_exec($ch);
        if(curl_errno($ch)){
            $msg = curl_error($ch);
            curl_close($ch);
            return array("error"=>500, "msg"=>$msg, "result"=>false);
        }else{
            curl_close($ch);
            return json_decode($result, true);
        }
    }

    /*
     * 参数签名
     * @param $secret 应用密钥
     * @param $params 签名参数
     * */
    function generateSign($params){
        ksort($params);
        $buff="";
        foreach($params as $key=>$value){
            $buff .=$key;
            $buff .=$value;
        }
        $buff .= $this->secret;
        return md5(mb_convert_encoding($buff, "utf8", "auto"));
    }
}