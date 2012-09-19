<?php
namespace undertow\dependencyinjection;
interface EventInterface {
    # Should just trigger an event and the return of the trigger is what's important
    public function trigger($eventName);
    public function register($eventName, $callable);
}