<?php
declare( encoding = "UTF8" ) ;
namespace undertow\autoload;

/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-07-24 01:46
 *
 */
class Autoload {

    private function namespaceInjection($object, $inject_to){
   		try{
   			if ( class_exists($inject_to,true) ){
   				return false;
   			}
   		}
   		catch(\Exception $e){

   		}
   		$__object__ = explode('\\',(is_object($inject_to)? get_class($inject_to):$inject_to));
   		# check if inject_to actually exists...
   		$class = array_pop($__object__);
   		$ns    = trim(implode('\\',$__object__),'\\');
   		if ($ns == ''){
   			$eval = sprintf(
   					'namespace{class %s extends \\%s{}}',
   					$class,
   					$object
   			);
   		}
   		else{
   			$eval = sprintf(
   					'namespace %s; class %s extends \\%s{}',
   					$ns,
   					$class,
   					$object
   			);
   		}
   		# echo $eval;
  		eval($eval);    # eval is an evil evil servant but we need him here.
    }
}
