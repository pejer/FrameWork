<?php
declare(encoding="UTF8");
namespace undertow\kernel;
/**
 * Created by JetBrains PhpStorm.
 * User: henrikpejer
 * Date: 2012-08-14
 * Time: 21:52
 * To change this template use File | Settings | File Templates.
 */
class HttpKernel extends Kernel{

    function loadRequest() {
        $this->request = $this->DIget('\\undertow\\request\\HttpRequest');
    }
}