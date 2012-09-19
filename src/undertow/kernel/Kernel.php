<?php
declare( encoding = "UTF8" ) ;
namespace undertow\kernel;

/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-07-24 00:16
 *
 */
class Kernel implements kernelinterface{
    protected $preInstructions = array();
    protected $instructions = NULL;
    protected $postInstructions = array();
    protected $request = NULL;
    protected $routes = NULL;

    public function __construct($DI, array $instructions = NULL, array $routes = NULL, $Response){
        $this->DI = $DI;
        $this->instructions = $instructions;
        $this->routes = $routes;
        $this->response = $Response;
        $this->response->startOb();
        $this->loadRequest();
        $this->DIaddAlias($this->request,'Request');
        echo "Url is : ".$this->request->getUrl()."<br>";
    }

    public function DIget(){
        return call_user_func_array(array($this->DI,'get'),func_get_args());
    }

    public function DIaddAlias(){
        return call_user_func_array(array($this->DI,'addAlias'),func_get_args());
    }

    public function DIregister(){
        return call_user_func_array(array($this->DI,'register'),func_get_args());
    }

    public function DIgetParameter(){
        return call_user_func_array(array($this->DI,'getParameter'),func_get_args());
    }

    public function DIsetParameter(){
        return call_user_func_array(array($this->DI,'setParameter'),func_get_args());
    }

    public function runInstructions(){
        foreach($this->instructions as $instruction => $argsArray){
            # lets call event
        }
    }

    public function loadRequest() {
    }

    public function loadResponse() {
    }

    public function setInstructions($instructions){
        $this->instructions = $instructions;
    }

    public function setRoutes($routes){
        $this->routes = $routes;
    }

    public function boot(){
        $router = $this->DIget('Router');
        $route = $router->matchRoutes($this->routes);
        if($route == FALSE){
            echo "Oh darn it : 404 DUDE!<br>";
        }else{
            $o = $this->DIget($route->controller);
            call_user_func_array(array($o, $route->method),$route->args);
        }
        $this->response->sendBody();
    }
}
