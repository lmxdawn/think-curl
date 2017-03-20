<?php

/*
 * curl 请求类
 *
 * @auther  ken<lmxdawn@gmail.com>
 * @time    2016-12-16
 */

namespace lmxdawn\curl;

class Curl {


    //请求的token
    const token='yangyulong';

    //请求url
    private $url;

    //请求的类型
    private $requestType;

    //请求的数据
    private $params;

    //curl实例
    private $curl;
    //curl 的选择列表
    private $opts = [];

    //状态
    public $status;

    //头信息
    private $headers = array();

    //最后一次的错误信息
    public $error;

    //请求返回的资源
    public $res;


    public function __construct($url, $params = [], $header = []) {

        if (!$url) {
            $this->error = '未设置请求地址';
            return false;
        }
        //初始化类中的数据
        $this->url = $url;//地址
        $this->params = $params;//请求参数
        $this->headers = $header;//请求头信息
        $opts = [
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header,
        ];
        $this->opts = $opts;

        try{
            if(!$this->curl = curl_init()){
                throw new \Exception('curl初始化错误：');
            };
        }catch (\Exception $e){
            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';
        }

    }


    public function _get() {
        $this->requestType = 'GET';
        $this->opts[CURLOPT_URL] = $this->url . '?' . (is_string($this->params) ? $this->params : http_build_query($this->params));
        return $this;
    }

    public function _post(){
        $this->requestType = 'POST';
        $isJson = is_string($this->params);
        if($isJson){
            $this->setHeader(['Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($this->params)]);
        }else{
            $this->params = http_build_query($this->params);
        }
        $this->opts[CURLOPT_URL]        = $this->url;
        $this->opts[CURLOPT_POST]       = 1;
        $this->opts[CURLOPT_POSTFIELDS] = $this->params;
        return $this;
    }

    public function _put(){
        $this->requestType = 'PUT';
        $isJson = is_string($this->params);
        if($isJson){
            $this->setHeader(['Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($this->params)]);
        }else{
            $this->params = http_build_query($this->params);
        }
        $this->opts[CURLOPT_URL]        = $this->url;
        $this->opts[CURLOPT_CUSTOMREQUEST]    = 'PUT';
        $this->opts[CURLOPT_POSTFIELDS] = $this->params;
        return $this;
    }

    public function _delete() {
        $this->requestType = 'DELETE';
        $this->opts[CURLOPT_URL] = $this->url . '?' . (is_string($this->params) ? $this->params : http_build_query($this->params));
        $this->opts[CURLOPT_CUSTOMREQUEST]    = 'DELETE';
        return $this;
    }


    public function doRequest() {
        //发送给服务端验证信息
        if((null !== self::token) && self::token){
            $this->headers = array(
                'Client-Token:'.self::token,//此处不能用下划线
                'Client-Code:'.$this->setAuthorization()
            );
        }

        //执行curl请求
        curl_setopt_array($this->curl, $this->opts);
        //设置错误信息
        $this->error = curl_error($this->curl);
        $res = curl_exec($this->curl);

        //获取curl执行状态信息
        $this->status = $this->getInfo();
        $this->res = $res;
        return $res;
    }


    /**
     * 设置发送的头部信息
     */
    private function setHeader($data = []){
        $this->opts[CURLOPT_HTTPHEADER] = array_merge($this->opts[CURLOPT_HTTPHEADER],$data);
    }

    /**
     * 生成授权码
     * @return string 授权码
     */
    private function setAuthorization(){
        $authorization = md5(substr(md5(self::token), 8, 24).self::token);
        return $authorization;
    }
    /**
     * 获取curl中的状态信息
     */
    public function getInfo(){
        return curl_getinfo($this->curl);
    }

    /**
     * 关闭curl连接
     */
    public function __destruct(){
        curl_close($this->curl);
    }

}