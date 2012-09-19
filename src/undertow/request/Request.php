<?php
declare(encoding = "UTF8") ;
namespace undertow\request;

class Request {
    protected $_POST, $_GET, $_FILES, $_COOKIE, $_SERVER;

    public function __construct($get = array(), $post = array(), $files = array(), $cookies = array(), $server = array()) {
        $this->_GET    = $get;
        $this->_POST   = $post;
        $this->_FILES  = $files;
        $this->_COOKIE = $cookies;
        $this->_SERVER = $server;
    }

    public function getUrl() {
        return '';
    }
}
