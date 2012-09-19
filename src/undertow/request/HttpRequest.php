<?php
declare(encoding = "UTF8");
namespace  undertow\request;

class HttpRequest extends request{

    public function __construct(){
        parent::__construct($_GET, $_POST,$_FILES,$_COOKIE, $_SERVER);
    }

    public function getUrl(){
        static $url = NULL;
        if(!isset($url)){
            $url = isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:$_SERVER['REQUEST_URI'];
            $url = trim($url,'/ ');
        }
        return $url;
    }

    public function getMethod(){
        return $this->_SERVER['REQUEST_METHOD'];
    }
}