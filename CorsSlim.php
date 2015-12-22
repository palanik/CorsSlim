<?php
namespace CorsSlim;

// https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
class CorsSlim {
    protected $settings;

    public function __construct($settings = array()) {
        $this->settings = array_merge(array(
                'origin' => '*',    // Wide Open!
                'allowMethods' => 'GET,HEAD,PUT,POST,DELETE'
                ), $settings);
    }

    protected function setOrigin($req, $rsp) {
        $origin = $this->settings['origin'];
        if (is_callable($origin)) {
            // Call origin callback with request origin
            $origin = call_user_func($origin,
                                    $req->getHeader("Origin")
                                    );
        }

        // handle multiple allowed origins
        if(is_array($origin)) {

            $allowedOrigins = $origin;

            $origin = null;

            // but use a specific origin if there is a match
            foreach($allowedOrigins as $allowedOrigin) {
                foreach($req->getHeader("Origin") as $reqOrig) {
                    if($allowedOrigin === $reqOrig) {
                        $origin = $allowedOrigin;
                        break;
                    }
                }
                if (!is_null($origin)) {
                    break;
                }
            }

            if (is_null($origin)) {
                // default to the first allowed origin
                $origin = reset($allowedOrigins);                
            }
        }

        return $rsp->withHeader('Access-Control-Allow-Origin', $origin);
    }

    protected function setExposeHeaders($req, $rsp) {
        if (isset($this->settings['exposeHeaders'])) {
            $rsp = $rsp->withAddedHeader('Access-Control-Expose-Headers', $this->settings['exposeHeaders']);
        }

        return $rsp;
    }
    
    protected function setMaxAge($req, $rsp) {
        if (isset($this->settings['maxAge'])) {
            $rsp = $rsp->withHeader('Access-Control-Max-Age', $this->settings['maxAge']);
        }

        return $rsp;
    }

    protected function setAllowCredentials($req, $rsp) {
        if (isset($this->settings['allowCredentials']) && $this->settings['allowCredentials'] === True) {
            $rsp = $rsp->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $rsp;
    }

    protected function setAllowMethods($req, $rsp) {
        if (isset($this->settings['allowMethods'])) {
            $rsp = $rsp->withHeader('Access-Control-Allow-Methods', $this->settings['allowMethods']);
        }

        return $rsp;
    }

    protected function setAllowHeaders($req, $rsp) {
        if (isset($this->settings['allowHeaders'])) {
            $allowHeaders = $this->settings['allowHeaders'];
        }
        else {  // Otherwise, use request headers
            $allowHeaders = $req->getHeader("Access-Control-Request-Headers");
        }

        if (isset($allowHeaders)) {
            $rsp = $rsp->withHeader('Access-Control-Allow-Headers', $allowHeaders);
        }

        return $rsp;
    }

    protected function setCorsHeaders($req, $rsp) {
        // http://www.html5rocks.com/static/images/cors_server_flowchart.png
        // Pre-flight
        if ($req->isOptions()) {
            $rsp = $this->setOrigin($req, $rsp);
            $rsp = $this->setMaxAge($req, $rsp);
            $rsp = $this->setAllowCredentials($req, $rsp);
            $rsp = $this->setAllowMethods($req, $rsp);
            $rsp = $this->setAllowHeaders($req, $rsp);
        }
        else {
            $rsp = $this->setOrigin($req, $rsp);
            $rsp = $this->setExposeHeaders($req, $rsp);
            $rsp = $this->setAllowCredentials($req, $rsp);
        }

        return $rsp;
    }

    public function __invoke($request, $response, $next) {
        $response = $this->setCorsHeaders($request, $response);
        if(!$request->isOptions()) {
            $response = $next($request, $response);
        }

        return $response;
    }

    public static function routeMiddleware($settings = array()) {
        $cors = new CorsSlim($settings);
        return $cors;
    }
}
?>
