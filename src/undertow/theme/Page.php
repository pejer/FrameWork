<?php
declare(encoding = "UTF8") ;
namespace undertow\theme;
use \undertow\event\Event as Event;

/**
 * User: Henrik Pejer mr@henrikpejer.com
 * Date: 2012-09-08 20:46
 */
class Page {

    protected $templateFile, $templateFileContents, $lineProcessed;
    protected $data = array();

    protected $docTypes = array(
        'html5'        => '!DOCTYPE html',
        'transitional' => '!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"',
        'strict'       => '!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"',
        'frameset'     => '!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"',
        '1.1'          => '!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"',
        'basic'        => '!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd"',
        'mobile'       => '!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd"');


    function __construct($file, Event $Event) {
        $this->templateFile = $file;
        $this->event        = $Event;
        $this->event->register('page.handleTag',array($this,'checkTag'));
        $this->event->register('page.handleEndTag', array($this, 'checkEndTag'));
    }

    function setData($data, $value = NULL) {
        if (isset($value)) {
            $this->data[$data] = $value;
        }
        else {
            $this->data = array_merge($this->data, $data);
        }
    }

    function render() {
        # lets handle this then
        #$this->templateFileContents = file_get_contents($this->templateFile);
        $this->renderTags();
        $this->handleCommands();
        return $this->templateFileContents;
    }

    protected function renderTags() {
        $lines       = file($this->templateFile);
        $openTags    = array();
        $indentLen   = NULL;
        $indentLevel = 0;
        foreach ($lines as $this->lineProcessed) {
            $line = rtrim(str_replace("\t", '    ', $this->lineProcessed));
            if($line{0} === '.'){
                $this->templateFileContents .= substr($line,1);
                continue;
            }
            preg_match('#([\040]{0,})([a-z0-1]+)([\.\#]{0,1}[^\.\#\( ]{0,})([\.\#]{0,1}[^\.\#\( ]{0,})(.*$)#i', $line, $matches);
            $line = trim($line);
            if ($line{0} == '#' || $line{0} == '.') {
                preg_match("#^([^\#\.]{0,})#", $this->lineProcessed, $lineMatches);
                $matches[1] = $lineMatches[1];
                if (empty($matches[3])) {
                    $matches[3] = $line{0} . $matches[2];
                }
                else {
                    $matches[4] = $line{0} . $matches[2];
                }
                $matches[2] = 'div';
            }
            $len        = strlen($matches[1]);
            $tag        = $matches[2];
            $content    = trim($matches[5]);
            $attributes = array();
            if (!empty($content) && $content{0} == '(') {
                preg_match("#\((.*)\)(.*$)#", $content, $attrMatch);
                $content    = trim($attrMatch[2]);
                $attribs    = explode(',', $attrMatch[1]);
                $attributes = '';
                foreach ($attribs as $attrib) {
                    $attributes[substr($attrib, 0, strpos($attrib, '='))] = substr($attrib, (strpos($attrib, '=') + 1));
                }
            }
            $class = NULL;
            $id    = NULL;
            if (!empty($matches[3]) && $matches[3]{0} == '.') {
                $attributes['class'] = '"' . substr($matches[3], 1) . '"';
            }
            if (!empty($matches[3]) && $matches[3]{0} == '#') {
                $attributes['id'] = '"' . substr($matches[3], 1) . '"';
            }
            if (!empty($matches[4]) && $matches[4]{0} == '.') {
                $attributes['class'] = '"' . substr($matches[4], 1) . '"';
            }
            if (!empty($matches[4]) && $matches[4]{0} == '#') {
                $attributes['id'] = '"' . substr($matches[4], 1) . '"';
            }
            if ($len > $indentLen) {
                $indentLevel++;
                $indentLen = $len;
            }
            elseif ($len < $indentLen) {
                for ($i = $indentLen; $i >= $len; $i--) {
                    if (!isset($openTags[$i])) {
                        continue;
                    }
                    $this->endTag($openTags[$i], $indentLevel);
                    unset($openTags[$i]);
                }
                $indentLevel--;
                $indentLen = $len;
            }
            elseif (isset($openTags[$len])) {
                $this->endTag($openTags[$len], $indentLevel);
            }
            $openTags[$len] = $tag;
            $this->startTag($tag, $attributes, $content, $indentLevel);
            $tag = NULL;
        }
        $openTags    = array_reverse($openTags);
        $indentLevel = sizeof($openTags) - 1;
        foreach ($openTags as $tag) {
            $this->endTag($tag, $indentLevel);
            --$indentLevel;
        }
    }

    protected function startTag($tag, $attributes = NULL, $content = NULL, $level = NULL) {
        $this->templateFileContents .= "\n";
        if (isset($level)) {
            #$this->templateFileContents .= str_pad("",$level,"\t");
        }
        $this->event->trigger('page.handleTag', $tag,$attributes,$content);
        $this->templateFileContents .= "<$tag";
        if (isset($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $this->templateFileContents .= " {$attribute}={$value}";
            }
        }
        $this->templateFileContents .= ">{$content}";
    }

    protected function endTag($tag, $level = NULL) {
        $this->event->trigger('page.handleEndTag', $tag);
        if (isset($tag)) {
            if (isset($level)) {
                #$this->templateFileContents .= str_pad("",$level,"\t");
            }
            $this->templateFileContents .= "</{$tag}>\n";
        }
    }

    public function checkEndTag(&$tag) {
        switch ($tag) {
            case 'doctype':
                $tag = NULL;
                break;
        }
    }

    public function checkTag(&$tag,&$attributes, &$content) {
        switch ($tag) {
            case 'doctype':
                $parts = explode(' ',trim($this->lineProcessed));
                if(isset($parts[1]) && isset($this->docTypes[$parts[1]])){
                    $tag = $this->docTypes[$parts[1]];
                }else{
                    $tag = $this->docTypes['html5'];
                }
                $content = '';
            break;
        }
    }

    protected function handleCommands(){
        # match all {{CONTENT}}
        preg_match_all("#\{\{([^\}]+)\}\}#", $this->templateFileContents,$matches);
        foreach($matches[1] as $key => $match){
            if(strpos($match,':') === FALSE){
                continue;
            }
            list($command,$value) = explode(':',$match,2);
            $this->templateFileContents = str_replace($matches[0][$key],$this->templateCommand($command,$value),$this->templateFileContents);
        }
        $this->injectData();
    }

    # TODO : call a hook...
    protected function templateCommand($command,$values){
        $return = '';
        switch($command){
            case 'include':
                $__tempPage__ = new Page(dirname($this->templateFile).DIRECTORY_SEPARATOR.$values, $this->event);
                $__tempPage__->setData($this->data);
                $return = $__tempPage__->render();
            break;
        }
        return $return;
    }

    protected function injectData() {
        extract($this->data);
        $this->templateFileContents = preg_replace("/\{\{([^\{]{1,100}?)\}\}/e", "$$1", $this->templateFileContents);
    }
}
