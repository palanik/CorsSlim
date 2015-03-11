<?php
namespace CorsSlim;

// https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
class CorsSlim extends \Slim\Middleware {
    protected $settings;

    public function __construct($settings = array()) {
        $this->settings = array_merge(array(
                'origin' => '*'    // Wide Open!
                ), $settings);
    }

    protected function setOrigin($req, $rsp) {
        $origin = $this->settings['origin'];
        if (is_callable($origin)) {
            // Call origin callback with request origin
            $origin = call_user_func($origin,
                                    $req->headers->get("Origin")
                                    );
        }
        $rsp->headers->set('Access-Control-Allow-Origin', $origin);
    }

    protected function setExposeHeaders($req, $rsp) {
        if (isset($this->settings->exposeHeaders)) {
            $exposeHeaders = $this->settings->exposeHeaders;
            if (is_array($exposeHeaders)) {
                $exposeHeaders = implode(", ", $exposeHeaders);
            }
            
            $rsp->headers->set('Access-Control-Expose-Headers', $exposeHeaders);
        }
    }

    protected function setMaxAge($req, $rsp) {
        if (isset($this->settings['maxAge'])) {
            $rsp->headers->set('Access-Control-Max-Age', $this->settings['maxAge']);
        }
    }

    protected function setAllowCredentials($req, $rsp) {
        if (isset($this->settings['allowCredentials']) && $this->settings['allowCredentials'] === True) {
            $rsp->headers->set('Access-Control-Allow-Credentials', 'true');
        }
    }

    protected function setAllowMethods($req, $rsp) {
        if (isset($this->settings['allowMethods'])) {
            $allowMethods = $this->settings['allowMethods'];
            if (is_array($allowMethods)) {
                $allowMethods = implode(", ", $allowMethods);
            }
            
            $rsp->headers->set('Access-Control-Allow-Methods', $allowMethods);
        }
    }

    protected function setAllowHeaders($req, $rsp) {
        if (isset($this->settings['allowHeaders'])) {
            $allowHeaders = $this->settings['allowHeaders'];
            if (is_array($allowHeaders)) {
                $allowHeaders = implode(", ", $allowHeaders);
            }
        }
        else {  // Otherwise, use request headers
            $allowHeaders = $req->headers->get("Access-Control-Request-Headers");
        }

        if (isset($allowHeaders)) {
            $rsp->headers->set('Access-Control-Allow-Headers', $allowHeaders);
        }
    }

    protected function setCorsHeaders($app) {
        $req = $app->request();
        $rsp = $app->response();

        $this->setOrigin($req, $rsp);
        $this->setExposeHeaders($req, $rsp);
        $this->setMaxAge($req, $rsp);
        $this->setAllowCredentials($req, $rsp);
        $this->setAllowMethods($req, $rsp);
        $this->setAllowHeaders($req, $rsp);
    }

    public function call() {
        if($this->app->request->isOptions()) {
        	$this->setCorsHeaders($this->app);
	} else {
        	$this->next->call();
        }
    }
}
?>
