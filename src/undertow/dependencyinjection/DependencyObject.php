<?php
declare( encoding = "UTF8" ) ;
namespace undertow\dependencyinjection;

/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-07-24 22:23
 *
 */
class DependencyObject {
    protected  $instance, $classToInstantiate, $args, $methods;

    public function __construct($class) {
        $this->methods            = array();
        $this->args               = array();
        $this->classToInstantiate = $class;
    }

    public function get(){
        return array('class'=> $this->classToInstantiate, 'args' => $this->args);
    }

    public function setArguments(Array $args) {
        $this->args = $args;
        return $this;
    }

    public function addMethodCall($method, $args = NULL) {
        $this->methods[] = array('method'=> $method, 'args'=> $args);
        return $this;
    }
}
