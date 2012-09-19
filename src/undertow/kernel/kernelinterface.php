<?php
declare( encoding = "UTF8" ) ;
namespace undertow\kernel;

interface kernelinterface {
    function runInstructions();
    function loadRequest();
    function loadResponse();
}
