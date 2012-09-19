<?php
declare( encoding = "UTF8" ) ;

$constants = array(
    'BENCHMARK_START_TIME'   => function(){return microtime(TRUE);},
    'UNDERTOW_ROOT'          => function(){return __DIR__;},
    'APP_ROOT'               => function(){trigger_error('No APP_ROOT defined, falling back to default.');return __DIR__;}, # this _should_ throw some sort of error... or exception perhaps?
    'D_S'                    => function(){return DIRECTORY_SEPARATOR;}
);
foreach($constants as $constant => $value){
    if( FALSE === defined($constant)){
        define($constant, $value());
    }
}