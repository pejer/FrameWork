<?php
declare(encoding = "UTF8") ;
namespace undertow\dependencyinjection;
use undertow\dependencyinjection\ContainerOfObjects;
use undertow\dependencyinjection\DependencyObject;

/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-07-24 00:15
 *
 */
class DependencyInjection {
    protected $container = NULL;

    public function __construct(ContainerOfObjects $container, EventInterface $event = NULL) {
        $this->container               = $container;
        $this->container['objects']    = array();
        $this->container['parameters'] = array();
        $this->container['classes']    = array();
        $this->event                   = $event;

    }

    public function get($name) {
        if(isset($this->event)){
            $this->event->filter('DI.get',$name);
        }
        switch(TRUE){
            case (!isset($this->container['objects'][$name]) && !isset($this->container['classes'][$name])):
                $__obj__ = NULL;
                $__args__ = func_get_args();
                array_shift($__args__);
                $__TempObj__ = $this->instantiateClass($name,$__args__);
                if(!empty($__TempObj__)){
                    $__obj__ = $__TempObj__;
                }
            break;
            case (isset($this->container['objects'][$name])):
                $__obj__ = $this->container['objects'][$name];
            break;
            case (isset($this->container['classes'][$name])):
                $__obj__ = $this->container['classes'][$name];
            break;
        }
        if (is_string($__obj__) && isset($this->container['objects'][$__obj__])) {
            $__obj__ = $this->container['objects'][$__obj__];
        }
        if ($__obj__ instanceof DependencyObject) {
            $__toInit__ =  $__obj__->get();
            $__obj__ = $this->instantiateClass($__toInit__['class'],$__toInit__['args']);
            if(isset($this->container['objects'][$name]) && $this->container['objects'][$name] instanceof DependencyObject){
                $this->container['objects'] = array_merge($this->container['objects'], array($name => $__obj__));
            }
        }
        return $__obj__;
    }

    public function setParameter($name, $value) {
        $this->container['parameters'] = array_merge($this->container['parameters'], array($name => $value));
    }

    public function getParameter($name, $default = NULL) {
        return isset($this->container['parameters'][$name]) ? $this->container['parameters'][$name] : $default;
    }

    public function addAlias($object, $name) {
        $this->container['objects'] = array_merge($this->container['objects'], array($name=> $object));
        return $object;
    }

    public function register($name, $class) {
        $this->container['objects'] =
                array_merge($this->container['objects'], array($name=> new DependencyObject($class)));
        $this->container['classes'] =
                array_merge($this->container['classes'], array($class => $this->container['objects'][$name]));
        return $this->container['objects'][$name];
    }

    public function instantiateClass($classToLoad, $args) {
        $__class__            = new \ReflectionClass($classToLoad);
        $__classConstructor__ = $__class__->getConstructor();
        if ($__classConstructor__) {
            $__params__ = $__classConstructor__->getParameters();
            if ($__params__) {
                foreach ($__params__ as $key => $__param__) {
                    if (isset($args[$key])) {
                        continue;
                    }
                    switch (TRUE) {
                        case isset($this->container['objects'][$__param__->getName()]):
                            $args[$key] = $this->container['objects'][$__param__->getName()];
                            if($args[$key] instanceof DependencyObject){
                                $__toInit__ =  $args[$key]->get();
                                $args[$key] = $this->instantiateClass($__toInit__['class'],$__toInit__['args']);
                            }
                        break;
                        case $__param__->getClass():
                            $__DIClass__ = $__param__
                                    ->getClass()
                                    ->getName();

                            if (NULL !== $__DIClass__ && isset($this->container['classes'][$__DIClass__])) {
                                $args[$key] = $this->container['classes'][$__DIClass__];
                            }else{
                                try{
                                    $__obj__ = $this->instantiateClass($__DIClass__, array());
                                    $args[$key] = $__obj__;
                                }catch(\Exception $e){
                                    var_dump($e->getMessage());
                                }
                            }
                        break;
                        case $__param__->allowsNull():
                            $args[$key] = NULL;
                        break;
                        default:
                            $args[$key] = $__param__->getDefaultValue();
                        break;
                    }
                }
            }
        }
        return count($args) == 0 ? $__class__->newInstance() : $__class__->newInstanceArgs($args);
    }
}
