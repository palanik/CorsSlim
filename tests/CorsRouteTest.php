<?php
namespace CorsSlim\Tests;

use Slim\HttpCache\Cache;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Http\Headers;
use Slim\Http\Body;
use Slim\Http\Collection;

class CorsSlimRouteTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        //ob_start();
    }
    public function tearDown() {
        //ob_end_clean();
    }

    private function runApp($action, $actionName, $mwOptions = NULL, $headers = array()) {
        $uri = Uri::createFromString('http://localhost/'. $actionName);
        $reqHeaders = new Headers();
        foreach ($headers as $key => $value) {
            $reqHeaders->set($key, $value);
        }

        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $req = new Request('GET', $uri, $reqHeaders, $cookies, $serverParams, $body);
        $res = new Response();

        $next = function (Request $req, Response $res) use ($action) {
            $res = $res->withHeader('Content-type', 'application/json');
            $name = substr($req->getUri()->getPath(), 1);
            return $res->write(json_encode(array(
                                                "action" => $action,
                                                "method" => "GET",
                                                "name" => $name
                                                )
                                            )
                                    );
        };


        $mw = function($req, $res, $next) {
            return $next($req, $res); // Do nothing
        };
        if (isset($mwOptions)) {
            if (is_callable($mwOptions)) {
                $mw = $mwOptions;
            }
            else {
                $mwOptions['appName'] = $actionName;
                $mw = \CorsSlim\CorsSlim::routeMiddleware($mwOptions);
            }
        }

        $res = $mw($req, $res, $next);
        $this->validate($res, 'GET', $action, $actionName);

        return $res;
    }

    private function runAppHead($action, $actionName, $mwOptions = NULL, $headers = array()) {
        $uri = Uri::createFromString('http://localhost/'. $actionName);
        $reqHeaders = new Headers();
        foreach ($headers as $key => $value) {
            $reqHeaders->set($key, $value);
        }

        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $req = new Request('HEAD', $uri, $reqHeaders, $cookies, $serverParams, $body);
        $res = new Response();

        $next = function (Request $req, Response $res) use ($action) {
            if ($req->isHead()) {
                return $res->withStatus(204);
            }
            $res = $res->withHeader('Content-type', 'application/json');
            $name = substr($req->getUri()->getPath(), 1);
            return $res->write(json_encode(array(
                                                "action" => $action,
                                                "method" => "GET",
                                                "name" => $name
                                                )
                                            )
                                    );
        };


        $mw = function($req, $res, $next) {
            return $next($req, $res); // Do nothing
        };
        if (isset($mwOptions)) {
            if (is_callable($mwOptions)) {
                $mw = $mwOptions;
            }
            else {
                $mwOptions['appName'] = $actionName;
                $mw = \CorsSlim\CorsSlim::routeMiddleware($mwOptions);
            }
        }

        $res = $mw($req, $res, $next);
        $this->assertEquals(204, $res->getStatusCode());

        return $res;
    }

    private function runAppPost($action, $actionName, $mwOptions = NULL, $headers = array()) {
        $uri = Uri::createFromString('http://localhost/'. $actionName);
        $reqHeaders = new Headers();
        foreach ($headers as $key => $value) {
            $reqHeaders->set($key, $value);
        }

        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $req = new Request('POST', $uri, $reqHeaders, $cookies, $serverParams, $body);
        $res = new Response();

        $next = function (Request $req, Response $res) use ($action) {
            if ($req->isHead()) {
                return $res->withStatus(204);
            }
            $res = $res->withHeader('Content-type', 'application/json');
            $name = substr($req->getUri()->getPath(), 1);
            return $res->write(json_encode(array(
                                                "action" => $action,
                                                "method" => "POST",
                                                "name" => $name
                                                )
                                            )
                                    );
        };


        $mw = function($req, $res, $next) {
            return $next($req, $res); // Do nothing
        };
        if (isset($mwOptions)) {
            if (is_callable($mwOptions)) {
                $mw = $mwOptions;
            }
            else {
                $mwOptions['appName'] = $actionName;
                $mw = \CorsSlim\CorsSlim::routeMiddleware($mwOptions);
            }
        }

        $res = $mw($req, $res, $next);
        $this->validate($res, 'POST', $action, $actionName);

        return $res;
    }

    private function runAppPreFlight($action, $actionName, $mwOptions = NULL, $headers = array()) {
        $uri = Uri::createFromString('http://localhost/'. $actionName);
        $reqHeaders = new Headers();
        foreach ($headers as $key => $value) {
            $reqHeaders->set($key, $value);
        }

        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $req = new Request('OPTIONS', $uri, $reqHeaders, $cookies, $serverParams, $body);
        $res = new Response();

        $next = function (Request $req, Response $res) use ($action) {
            if ($req->isOptions()) {
                return $res;
            }
            $res = $res->withHeader('Content-type', 'application/json');
            $name = substr($req->getUri()->getPath(), 1);
            return $res->write(json_encode(array(
                                                "action" => $action,
                                                "method" => "DELETE",
                                                "name" => $name
                                                )
                                            )
                                    );
        };


        $mw = function($req, $res, $next) {
            return $next($req, $res); // Do nothing
        };
        if (isset($mwOptions)) {
            if (is_callable($mwOptions)) {
                $mw = $mwOptions;
            }
            else {
                $mwOptions['appName'] = $actionName;
                $mw = \CorsSlim\CorsSlim::routeMiddleware($mwOptions);
            }
        }

        $res = $mw($req, $res, $next);

        return $res;


        // \Slim\Environment::mock(array(
        //     'REQUEST_METHOD' => 'OPTIONS',
        //     'SERVER_NAME' => 'localhost',
        //     'SERVER_PORT' => 80,
        //     'ACCEPT' => 'application/json',
        //     'SCRIPT_NAME' => '/index.php',
        //     'PATH_INFO' => '/'. $actionName
        // ));
        // $app = new \Slim\Slim();
        // $app->setName($actionName);

        // $mw = function() {
        //     // Do nothing
        // };
        // if (isset($mwOptions)) {
        //     if (is_callable($mwOptions)) {
        //         $mw = $mwOptions;
        //     }
        //     else {
        //         $mwOptions['appName'] = $actionName;
        //         $mw = \CorsSlim\CorsSlim::routeMiddleware($mwOptions);
        //     }
        // }

        // $app->options('/:name', $mw, function ($name) use ($app, $action) {
        // });

        // $app->delete('/:name', $mw, function ($name) use ($app, $action) {
        //     if ($app->request->isHead()) {
        //         $app->status(204);
        //         return;
        //     }


        //     $app->contentType('application/json');
        //     $app->response->write(json_encode(array(
        //                                         "action" => $action,
        //                                         "method" => "DELETE",
        //                                         "name" => $name
        //                                         )
        //                                     )
        //                             );
        // });

        // foreach ($headers as $key => $value) {
        //     $app->request->headers()->set($key, $value);
        // }

        // $app->run();

        // return $app;
    }

    private function validate($res, $method, $action, $name) {
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertEquals("application/json", $res->getHeader("Content-Type")[0]);

        $content = json_decode($res->getBody());
        $this->assertEquals($action, $content->action);
        $this->assertEquals($method, $content->method);
        $this->assertEquals($name, $content->name);
    }

    public function testDefaultCors() {
        $res = $this->runApp('cors', 'DefaultCors', []);
        $this->assertEquals("*", $res->getHeader("Access-Control-Allow-Origin")[0]);
    }

    public function testCorsOrigin() {
        $res = $this->runApp('cors-origin', 'CorsOrigin', array("origin" => "*"));
        $this->assertEquals("*", $res->getHeader("Access-Control-Allow-Origin")[0]);
    }

    public function testCorsOriginSingle() {
        $res = $this->runApp('cors-origin-single', 'CorsOriginSingle', array("origin" => "http://github.com", "appName" => "CorsOriginSingle"));
        $this->assertEquals("http://github.com", $res->getHeader("Access-Control-Allow-Origin")[0]);
    }

    public function testCorsOriginArray() {
        $res = $this->runApp('cors-origin-array', 'CorsOriginArray', array("origin" => array("http://mozilla.com", "http://php.net", "http://github.com")));
        $this->assertEquals("http://mozilla.com", $res->getHeader("Access-Control-Allow-Origin")[0]);        
    }

    public function testCorsOriginArraySpecific() {
        $mwOptions = array("origin" => array("http://mozilla.com", "http://php.net", "http://github.com"));
        $headers = array('origin' => 'http://php.net');
        $res = $this->runApp('cors-origin-array-specific', 'CorsOriginArraySpecific', $mwOptions, $headers);
        $this->assertEquals("http://php.net", $res->getHeader("Access-Control-Allow-Origin")[0]);        
    }

    public function testCorsOriginCallable() {
        $mwOptions = array("origin" => function($reqOrigin) { return $reqOrigin;});
        $headers = array('origin' => 'http://www.slimframework.com/');
        $res = $this->runApp('cors-origin-callable', 'CorsOriginCallable', $mwOptions, $headers);
        $this->assertEquals("http://www.slimframework.com/", $res->getHeader("Access-Control-Allow-Origin")[0]);        
    }


    // Simple Requests
    public function testSimpleCorsRequestFail() {
        $res = $this->runApp('cors', 'SimpleCorsRequestFail');
        $this->assertTrue(empty($res->getHeader("Access-Control-Allow-Origin")));
    }

    public function testSimpleCorsRequest() {
        $res = $this->runApp('cors', 'SimpleCorsRequest', array());
        $this->assertEquals("*", $res->getHeader("Access-Control-Allow-Origin")[0]);
    }

    public function testSimpleCorsRequestHeadFail() {
        $res = $this->runAppHead('cors', 'SimpleCorsRequestHeadFail');
        $this->assertTrue(empty($res->getHeader("Access-Control-Allow-Origin")));
    }

    public function testSimpleCorsRequestHead() {
        $res = $this->runAppHead('cors', 'SimpleCorsRequestHead', array());
        $this->assertEquals("*", $res->getHeader("Access-Control-Allow-Origin")[0]);
    }

    public function testSimpleCorsRequestPostFail() {
        $res = $this->runAppPost('cors', 'SimpleCorsRequestPostFail');
        $this->assertTrue(empty($res->getHeader("Access-Control-Allow-Origin")));
    }

    public function testSimpleCorsRequestPost() {
        $res = $this->runAppPost('cors', 'SimpleCorsRequestPost', array());
        $this->assertEquals("*", $res->getHeader("Access-Control-Allow-Origin")[0]);
    }

    // Complex Requests (With Pre-Flight)
    public function testComplexCorsRequestPreFlightFail() {
        $res = $this->runAppPreFlight('cors', 'ComplexCorsRequestPreFlightFail');
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertTrue(empty($res->getHeader("Access-Control-Allow-Origin")));
    }

    public function testComplexCorsRequestPreFlight() {
        $res = $this->runAppPreFlight('cors', 'ComplexCorsRequestPreFlight', array());
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertEquals("*", $res->getHeader("Access-Control-Allow-Origin")[0]);
    }

    // Access-Control-Expose-Headers
    public function testAccessControlExposeHeaders() {
        $res = $this->runApp('cors', 'SimpleCorsRequestAccessControlExposeHeaders', array('exposeHeaders' => 'X-My-Custom-Header'));
        $this->assertEquals("X-My-Custom-Header", $res->getHeader("Access-Control-Expose-Headers")[0]);
    }

    public function testAccessControlExposeHeadersArray() {
        $res = $this->runApp('cors', 'SimpleCorsRequesAccessControlExposeHeadersArrayt', array('exposeHeaders' => array("X-My-Custom-Header", "X-Another-Custom-Header")));
        $this->assertEquals("X-My-Custom-Header", $res->getHeader("Access-Control-Expose-Headers")[0]);
        $this->assertEquals("X-Another-Custom-Header", $res->getHeader("Access-Control-Expose-Headers")[1]);
    }

    // Access-Control-Max-Age
    public function testAccessControlMaxAge() {
        $res = $this->runAppPreFlight('cors', 'SimpleCorsRequestAccessControlMaxAge', array('maxAge' => 1728000));
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertEquals(1728000, $res->getHeader("Access-Control-Max-Age")[0]);
    }
    // Access-Control-Allow-Credentials
    public function testAccessControlAllowCredentials() {
        $res = $this->runApp('cors', 'SimpleCorsRequestAccessControlAllowCredentials', array('allowCredentials' => True));
        $this->assertEquals("true", $res->getHeader("Access-Control-Allow-Credentials")[0]);
    }

    // Access-Control-Allow-Methods
    public function testAccessControlAllowMethods() {
        $res = $this->runAppPreFlight('cors', 'SimpleCorsRequestAccessControlAllowMethods', array('allowMethods' => array('GET', 'POST')));
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertEquals("GET", $res->getHeader("Access-Control-Allow-Methods")[0]);
        $this->assertEquals("POST", $res->getHeader("Access-Control-Allow-Methods")[1]);
    }

    // Access-Control-Allow-Headers
    public function testAccessControlAllowHeaders() {
        $res = $this->runAppPreFlight('cors', 'SimpleCorsRequestAccessControlAllowHeaders', array("allowHeaders" => array("X-PINGOTHER")));
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertEquals('X-PINGOTHER', $res->getHeader("Access-Control-Allow-Headers")[0]);
    }
}