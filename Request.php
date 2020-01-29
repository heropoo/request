<?php

namespace Moon\Request;

use Swoole\Http\Request as SwooleHttpRequest;
use Moon\Routing\Route;
use Moon\Session\Session;

class Request
{
    /** @var SwooleHttpRequest */
    protected $swooleHttpRequest;

    /** @var Session $session */
    protected $session;

    /** @var Route $route */
    protected $route;

    public $header;

    public $server;

    public $cookie;

    public $get;

    public $files;

    public $post;

    //public $tmpFiles;

    protected $basePath = '';

    private function __construct()
    {
    }

    public static function createFromGlobals()
    {
        $request = new static();
        $request->server = $_SERVER;
        $request->cookie = $_COOKIE;
        $request->files = $_FILES;
        //$request->tmpFiles = []; //todo
        $request->get = $_GET;
        $request->post = $_POST;

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $k = str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($key, 5))));
                $request->header[$k] = $value;
            }
        }
        return $request;
    }

    public static function createFromSwooleHttpRequest(SwooleHttpRequest $swooleHttpRequest)
    {
        $request = new static();
        $request->swooleHttpRequest = $swooleHttpRequest;

        $request->cookie = $swooleHttpRequest->cookie;
        $request->files = $swooleHttpRequest->files;
        $request->tmpFiles = $swooleHttpRequest->tmpfiles;
        $request->get = $swooleHttpRequest->get ?? [];
        $request->post = $swooleHttpRequest->post ?? [];

        $request->header = $swooleHttpRequest->header;
        if (isset($swooleHttpRequest->header['authorization']) && $tmp = base64_decode(substr($swooleHttpRequest->header['authorization'], 6))) {
            $request->server['PHP_AUTH_USER'] = strstr($tmp, ':', true);
            $request->server['PHP_AUTH_PW'] = substr(strstr($tmp, ':'), 1);
        }

        foreach ($swooleHttpRequest->server as $key => $value) {
            $request->server[strtoupper($key)] = $value;
        }

        return $request;
    }

    /**
     * @return SwooleHttpRequest
     */
    public function getSwooleHttpRequest()
    {
        return $this->swooleHttpRequest;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return strtoupper($this->server['REQUEST_METHOD']);
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        if (isset($this->server['PATH_INFO'])) {
            return $this->server['PATH_INFO'];
        }
        $uri = parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
        $this->basePath = dirname($this->server['SCRIPT_NAME']);
        $path = substr($uri, -(strlen($uri) - strlen($this->basePath)));
        return str_replace('//', '/', '/' . $path);
    }

    public function header($key, $default = null)
    {
        $key = strtolower($key);
        return $this->header[$key] ?? $default;
    }

    public function get($key, $default = null)
    {
        if (isset($this->get[$key])) {
            return $this->get[$key];
        } else if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        return $default;
    }

    public function all()
    {
        return array_merge($this->get, $this->post);
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return $this->server['QUERY_STRING'] ?? '';
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    public function setSession(Session $session)
    {
        $session->start();
        $this->session = $session;
    }

    public function setRoute(Route $route)
    {
        $this->route = $route;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function getRawContent()
    {
        if ($this->swooleHttpRequest instanceof SwooleHttpRequest) {
            return $this->swooleHttpRequest->rawContent();
        }
        return file_get_contents('php://input');
    }

    public function getBasePath()
    {
        return $this->basePath;
    }
}