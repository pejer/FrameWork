<?php
declare( encoding = "UTF8" ) ;
namespace undertow\storage;
use \undertow\event\Event;
/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-09-04 06:31
 *
 */

const OPEN_READONLY  = 'rb';
const OPEN_OVERWRITE = 'w+b';
const OPEN_AMEND     = 'a+b';
class File {
    protected $_fileHandle = NULL;
    protected $path = NULL;
    protected $_readable = NULL;
    protected $_writable = NULL;
    protected $exists = NULL;
    protected $isTemp = NULL;
    protected $chunkSize = 10;
    protected $openForWriting = FALSE;
    protected $writePosition = NULL;
    protected $readPosition = NULL;

    function __construct($fileToOpen = NULL, Event $Event = NULL) {
        if ( isset( $fileToOpen ) ) {
            $this->exists = file_exists($fileToOpen);
            if ( !file_exists($fileToOpen) ) {
                $this->path = $fileToOpen;
            } else {
                $path = realpath($fileToOpen);
                if ( is_file($path) && is_readable($path) && @stat($path) !== FALSE ) { # checks to make sure file really is a file
                    $this->path      = $path;
                    $this->_readable = is_readable($this->path);
                    $this->_writable = is_writable($this->path);
                }
            }
        }
        if ( isset( $Event ) ) {
            $that = &$this;
            $Event->register('system.end', function() use( $that ) {
                $that->close();
            });
        }
    }

    public function readPart($start, $length){
        # lets position the pointer at the start
        fseek($this->_fileHandle,$start);
        $return = NULL;
        $end = $start + $length;
        $stat = fstat($this->_fileHandle);
        if($end > $stat['size']){
            $end = $stat['size'];
        }
        $pos = ftell($this->_fileHandle);
        while($pos < $end){
            $len = ($pos + $this->chunkSize) > $end?($end - $pos):$this->chunkSize;
            $return .= fread($this->_fileHandle,$len);
            $pos = ftell($this->_fileHandle);
        }
        fseek($this->_fileHandle,$this->readPosition);
        return $return;
    }

    public function truncate() {
        $this->openFile(OPEN_OVERWRITE);
        $this->readPosition = $this->writePosition = ftell($this->_fileHandle);
    }

    public function amend($data) {
        $this->openFile(OPEN_AMEND);
        if ( isset( $this->readPosition ) ) {
            # so lets seek to the new position
            fseek($this->_fileHandle, $this->writePosition);
        }
        fwrite($this->_fileHandle, $data);
        $this->writePosition = ftell($this->_fileHandle);
    }

    public function read($len = NULL) {
        $this->openFile(OPEN_READONLY);
        $len    = $len == NULL ? $this->chunkSize : $len;
        $return = NULL;
        fseek($this->_fileHandle, $this->readPosition);
        try {
            while ($len > 0) {
                $return .= fread($this->_fileHandle, $this->chunkSize);
                $len -= $this->chunkSize;
                $this->readPosition = ftell($this->_fileHandle);
            }
        }
        catch (\Exception $e) {
        }
        return $return;
    }

    public function close() {
        $this->closeFile();
    }

    public function rewind(){
        $this->readPosition = 0;
    }

    public function delete(){
        $this->close();
        if(isset($this->path)){
            unlink($this->path);
        }
    }

    protected function openFile($fileAccessType) {
        # actually lets reopen the file, if, we want to write to it and it is only in read mode
        if ( $fileAccessType != OPEN_READONLY && $this->openForWriting == FALSE ) {
            $this->close();
        }
        if ( empty( $this->_fileHandle ) ) {
            if ( !isset( $this->path ) ) {
                $this->_fileHandle = tmpfile();
                $this->_readable   = TRUE;
                $this->_writable   = TRUE;
                $this->isTemp      = TRUE;
            } else {
                if(!file_exists($this->path)){  # could we create the file...?
                    touch($this->path);
                    $this->_readable = is_readable($this->path);
                    $this->_writable = is_writable($this->path);
                }
                # check if fileAccessType will work considering if we want to read/write to the file and if the file is read/writable
                if ( $fileAccessType == OPEN_READONLY && !$this->_readable
                  || ( $fileAccessType == OPEN_AMEND || $fileAccessType == OPEN_OVERWRITE ) && !$this->_writable
                ) {
                    $message = $fileAccessType == OPEN_READONLY && !$this->_readable ? 'File is not readable' : 'File is not read and/or writable';
                    throw new \RuntimeException( $message );
                }
                $this->_fileHandle = fopen($this->path, $fileAccessType);
                $this->isTemp      = FALSE;
            }
            $this->readPosition   = $this->readPosition = ftell($this->_fileHandle);
            $this->openForWriting = $fileAccessType == OPEN_READONLY ? FALSE : TRUE;
            $lockType = $fileAccessType == OPEN_READONLY?\LOCK_SH:\LOCK_EX;
            flock($this->_fileHandle,$lockType);
        }
    }

    protected function closeFile() {
        if ( isset( $this->_fileHandle ) ) {
            flock($this->_fileHandle,\LOCK_UN);
            fclose($this->_fileHandle);
            $this->_fileHandle = NULL;
            echo "Closing file...<br>";
        }
    }
}
