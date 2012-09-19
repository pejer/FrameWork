<?php
declare( encoding = "UTF8" ) ;
namespace undertow\response;
use \undertow\event\Event;
use \undertow\request\Request;

class Response {
    protected $headers = array('Server' => ' ', 'X-Powered-By' => 'Undertow by Tool');
    protected $data = NULL;
    protected $output = '';
    protected $headersSent = FALSE;
    protected $headerStatus = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    public function startOb(){
        # lets actually save all the output buffering here... for kicks. And right now, yes?
        ob_start(array(&$this, 'fallbackOb'));
    }

    public function fallbackOb($data) {
        $this->output .= $data;
        return $data;
    }

    public function redirect($url, $header = 301, $headerMessage = NULL) {
        $this->addHeader($header, $headerMessage);
        $this->addHeader('Location', $url);
        $this->sendHeaders();
    }

    public function addHeader($headerType, $headerValue = NULL) {
        $this->headers[$headerType] = $headerValue;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function sendBody() {
        ob_end_clean();
        if($this->data === NULL){
            $this->data = $this->output;
        }
        $this->sendHeaders();
        switch (gettype($this->data)) {
            case 'string':
                echo $this->data;
                break;
            case 'integer':
                break;
            case 'array':
                break;
        }
    }

    protected function sendHeaders() {
        if($this->headersSent){
            return TRUE;
        }
        foreach ($this->headers as $type => $value) {
            if ( !isset( $value ) && is_int($type) && isset( $this->headerStatus[$type] ) ) {
                if ( function_exists('\\http_response_code') ) {
                    \http_response_code($type);
                    continue;
                }
                header(' ', TRUE, $type);
                continue;
            }
            $header = $type;
            if ( isset( $value ) ) {
                $header = sprintf('%s: %s', $type, $value);
            }
            header($header, TRUE);
        }
        $this->headersSent = TRUE;
        return TRUE;
    }
}
