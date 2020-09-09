<?php

namespace Moon\Request;

use Moon\Routing\Route;
use Moon\Session\Session;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
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
        return file_get_contents('php://input');
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function getRequestTarget()
    {
        // TODO: Implement getRequestTarget() method.
    }

    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    public function getUri()
    {
        // TODO: Implement getUri() method.
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }


}