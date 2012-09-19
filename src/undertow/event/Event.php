<?php
declare(encoding = "UTF8") ;
namespace undertow\event;
use undertow\dependencyinjection\EventInterface;

const EVENT_ABORT = '32e4f1d38810d19529b4d0054eab52bd';
/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-07-24 01:30
 *
 * This little piggy will handle events throughout the system.
 * Objects can subscribe to events happening in the system and
 * functions will be called, with or without arguments.
 */
class Event implements EventInterface {

    protected $events = array();

    public function __construct() {
    }


    public function trigger($eventName, &$one = NULL, &$two = NULL, &$three = NULL, &$four = NULL, &$five = NULL, &$six = NULL, &$seven = NULL) {
        $args = func_get_args();
        $__return__ = NULL;
        switch (sizeof($args)) {
            case 1:
                $__return__ = $this->call($eventName);
                break;
            case 2:
                $__return__ = $this->call($eventName, $one);
                break;
            case 3:
                $__return__ = $this->call($eventName, $one, $two);
                break;
            case 4:
                $__return__ = $this->call($eventName, $one, $two, $three);
                break;
            case 5:
                $__return__ = $this->call($eventName, $one, $two, $three, $four);
                break;
            case 6:
                $__return__ = $this->call($eventName, $one, $two, $three, $four, $five);
                break;
            case 7:
                $__return__ = $this->call($eventName, $one, $two, $three, $four, $five, $six);
                break;
            case 8:
                $__return__ = $this->call($eventName, $one, $two, $three, $four, $five, $six, $seven);
                break;
        }
        return $__return__;
    }

    public function register($eventName, $callable) {
        if (!is_callable($callable)) {
            return FALSE;
        }
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = array();
        }
        $this->events[$eventName][] = $callable;
        return TRUE;
    }

    private function call($eventName, &$one = NULL, &$two = NULL, &$three = NULL, &$four = NULL, &$five = NULL, &$six = NULL, &$seven = NULL) {
        $return  = NULL;
        $numArgs = (func_num_args() - 1);
        if (isset($this->events[$eventName])) {
            # walk through the callables
            foreach ($this->events[$eventName] as $event) {
                $__return__ = NULL;
                switch ($numArgs) {
                    case 0:
                        if (is_array($event)) {
                            $__return__ = call_user_func($event);
                        }
                        else {
                            $__return__ = $event();
                        }
                        break;
                    case 1:
                        if (is_array($event)) {
                            $__return__ = call_user_func_array($event, array(&$one));
                        }
                        else {
                            $__return__ = $event($one);
                        }
                        break;
                    case 2:
                        if (is_array($event)) {
                            $__return__ = call_user_func_array($event, array(&$one, &$two));
                        }
                        else {
                            $__return__ = $event($one, $two);
                        }
                        break;
                    case 3:
                        if (is_array($event)) {
                            $__return__ = call_user_func_array($event, array(&$one, &$two, &$three));
                        }
                        else {
                            $__return__ = $event($one, $two, $three);
                        }
                        break;
                    case 4:
                        if (is_array($event)) {
                            $__return__ = call_user_func_array($event, array(&$one, &$two, &$three, &$four));
                        }
                        else {
                            $__return__ = $event($one, $two, $three, $four);
                        }
                        break;
                    case 5:
                        if (is_array($event)) {
                            $__return__ = call_user_func_array($event, array(&$one, &$two, &$three, &$four, &$five));
                        }
                        else {
                            $__return__ = $event($one, $two, $three, $four, $five);
                        }
                        break;
                    case 6:
                        if (is_array($event)) {
                            $__return__ = call_user_func_array($event, array(&$one, &$two, &$three, &$four, &$five, &$six));
                        }
                        else {
                            $__return__ = $event($one, $two, $three, $four, $five, $six);
                        }
                        break;
                    case 7:
                        if (is_array($event)) {
                            $__return__ = call_user_func_array($event, array(&$one, &$two, &$three, &$four, &$five, &$six, &$seven));
                        }
                        else {
                            $__return__ = $event($one, $two, $three, $four, $five, $six, $seven);
                        }
                        break;
                }
                if ($__return__ === EVENT_ABORT) {
                    break;
                }
                $return = $__return__;
            }
        }
        return $return;
    }
}
