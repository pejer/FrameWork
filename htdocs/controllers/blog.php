<?php
declare(encoding = "UTF8") ;
namespace controllers;
use \undertow\storage\File as File;
/**
 * User: Henrik Pejer mr@henrikpejer.com
 * Date: 2012-08-25 21:43
 */
class blog extends \undertow\controller\Controller{

    public function index($page){
        $d = $this->kernel->DIget('\\undertow\\theme\\Page', __DIR__.'/../theme/index.tpl');
        $d->setData('variableName','Henrik');
        echo "\n\n\n\n\n";
        echo $d->render();
        return false;
        # lets open a file
        echo APP_ROOT."test.txt<br>";
        #$file = new File(APP_ROOT.'test.txt');
        #ini_set('display_errors',1);
        #error_reporting(E_ALL);
        $file = $this->kernel->DIget('\\undertow\\storage\\File', APP_ROOT.'test.txt');
        $file->truncate();
        $file->amend('Hejsan svejsan!');
        var_dump($file->read());
        $file->amend('And this should magickly appear at the end of the file, right?');
        echo "File is now : ".file_get_contents(APP_ROOT.'test.txt').'<br>';
        var_dump($file->read());
        var_dump($file->readPart(37,1000));
        var_dump($file->read());
        echo "rewindindg read position...<br>";

        $file->rewind();
        var_dump($file->readPart(37,1000));
        $file->amend('The new ending...');
        while($part = $file->read()){
            echo $part;
        }
        $file->delete();
        $file->amend('Hi again!');
        echo "Displaying page {$page}<br>";
    }

    public function blogPost($title){
        echo "Lets view the post with the title \"{$title}\"<br>";
    }
}
