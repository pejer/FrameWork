<?php
declare(encoding="UTF8");
namespace undertow\controller;

class Controller {

    public function __construct(\undertow\event\Event $Event, $Kernel){
        $Event->register('*.pre',array($this, 'preEvent'));
        $Event->register('*.post',array($this, 'postEvent'));
        $this->kernel = $Kernel;
    }

    public function preEvent($eventName){

    }

    public function postEvent($eventName){

    }
}
