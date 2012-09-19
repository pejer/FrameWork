<?php
declare( encoding = "UTF8" ) ;
namespace app\lib;
use \DateTime;
/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-06-19 18:47
 *
 */

# todo : make this be able to actually tag the text, somehow, xml or other
class DateParse {
    protected $text, $formatedText, $matches;

    public function __construct($parse){
        date_default_timezone_set('Europe/Stockholm');
        $this->text = $parse;
        $this->cleanAndFormat();
        $this->match();
    }

    public function getMatches(){
        return $this->matches;
    }

    protected function cleanAndFormat(){
        $this->formatedText = ' '.preg_replace('#[\040]+#',' ',preg_replace('#[\s[:punct:]]+#',' ',strtolower($this->text))).' ';
        $this->formatedText = preg_replace('#([0-9]+) (am|pm)#i','${1}${2}',$this->formatedText);
        $this->formatedText = preg_replace('#\040[^0-9]{1,2}\040#',' ',$this->formatedText);
    }

    protected function match() {
    	$parse_array = explode(" ", $this->formatedText);
    	$phrases = array();
    	$max = count($parse_array);
    	for($key=0;$key<$max;$key++){
    		$word = $parse_array[$key];
    		if(is_numeric($word) || strlen($word) < 3){
    			continue;
    		}
    		if(strtotime($word) !== FALSE){
    			$phrase = $word;
    			# lets get the five words ahead,
    			for($i = ($key-1);$i>($key-5);$i--){
    				$__word__ = $parse_array[$i];
    				$__phrase__ = "{$__word__} {$phrase}";
    				try{
    					$_d_ = new DateTime($__phrase__);
    					$phrase = $__phrase__;
    				}catch(\Exception $e){
    					break;
    				}
    			}

    			for($i = ($key+1);$i<($key+5);$i++){
                    if(!isset($parse_array[$i])){
                        continue;
                    }
    				$__word__ = $parse_array[$i];
    				$__phrase__ = $phrase. " {$__word__}";
    				try{
    					$_d_ = new DateTime($__phrase__);
    					$phrase = $__phrase__;
    				}catch(\Exception $e){
    					break;
    				}
    			}
                $key = $i;
    			$phrases[$phrase] = $_d_;
    		}
    	}
    	$this->matches = $phrases;
        unset($phrase);
        unset($phrases);
        unset($__phrase__);
    }
}
