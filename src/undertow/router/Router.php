<?php
declare(encoding = "UTF8") ;
namespace undertow\router;
use \undertow\request\Request;
use \undertow\event\Event;
use \undertow\response\Response;

class Router {

    protected $defaultRouteValues = array(
        'method' =>'GET'
    );

    public function __construct(Request $Request, Event $Event, Response $Response) {
        $this->url           = explode('/', $Request->getUrl());
        $this->requestMethod = $Request->getMethod();
        $this->event         = $Event;
        $this->response      = $Response;
    }

    public function matchRoutes($routes = NULL) {
        # so lets figure out what route is the closest, ok....?
        if(isset($routes) && is_array($routes)){
            foreach ($routes as $route) {
                $route = array_merge($this->defaultRouteValues,$route);
                if ($route['method'] == '*' || $route['method'] == $this->requestMethod) {
                    if (FALSE !== ($args = $this->match($route))) {
                        if (isset($route['alias'])) {
                            $newRoute = $routes[$route['alias']['name']];
                            $args     = $route['alias']['args'];
                            $route    = $newRoute;
                        }
                        if (isset($route['redirect'])) {
                            $this->event->trigger('router.redirect', $route['redirect']);
                            $this->response->redirect($route['redirect']);
                        }
                        if (isset($route['alias'])) { # this is tricky - basically change URL and then rerun all the routes, again, for a match.

                        }
                        $__returnRoute__             = new \StdClass();
                        $__returnRoute__->controller = $route['controller'];
                        $__returnRoute__->method     = $route['function'];
                        $__returnRoute__->args       = $args;
                        return $__returnRoute__;
                    }
                }
            }
        }
        return FALSE;
    }

    public function makeRoute($route, $args) {
    }

    protected function match($route) {
        $__routeParts__ = explode('/', $route['url']);
        if (isset($route['args'])) {
            $args = array_values($route['args']);
        }
        else {
            $args = array();
        }
        $argsCounter = -1;
        $minArgs     = 0;
        $maxArgs     = 0;
        foreach ($__routeParts__ as $key => $__part__) {
            $__urlPart__ = isset($this->url[$key]) ? $this->url[$key] : NULL;
            $this->event->trigger('router.match.urlpart', $__urlPart__, $key, $route);
            if ($__part__[0] != '[') {
                if ($__part__ != $__urlPart__) {
                    return FALSE;
                }
                if ($__part__ == $__urlPart__) {
                    ++$minArgs;
                    ++$maxArgs;
                    continue;
                }
            }
            ++$argsCounter;
            ++$maxArgs;
            list($argName, $argType) = explode(':', substr($__part__, 1, -1));
            $allowedEmpty = FALSE;
            if (strpos($argType, '?') !== FALSE) {
                $allowedEmpty = TRUE;
                if (!isset($__urlPart__)) {
                    continue;
                }
            }
            else {
                ++$minArgs;
                if (empty($__urlPart__)) {
                    return FALSE;
                }
            }
            # match the type with the content
            switch (str_replace('?', '', $argType)) {
                case 'num':
                    $match = !preg_match("#[^0-9-_\.,]+#", $__urlPart__);
                    break;
                case 'alphanum':
                    $match = !preg_match("#[^0-9a-z-_\.,]+#i", $__urlPart__);
                    break;
                default:
                    return FALSE;
                    break;
            }
            if (FALSE == $match) {
                return FALSE;
            }
            else {
                $args[$argsCounter] = $__urlPart__;
            }
        }
        $length = sizeof($this->url);
        if ($length < $minArgs || $length > $maxArgs) {
            return FALSE;
        }
        return $args;
    }
}
