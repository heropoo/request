<?php
/**
 * @author Heropoo
 * @date 2020-09-09 23:20
 */

namespace Moon\Request;


use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    protected $headers = [];
    protected $protocolVersion;

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        return $this->protocolVersion = $version;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : [];
    }

    /**
     * @param string $name
     * @return string|void
     * @see https://www.php-fig.org/psr/psr-7/#headers-with-multiple-values
     */
    public function getHeaderLine($name)
    {
        $value = isset($this->headers[$name]) ? $this->headers[$name] : [];
        return implode(',', $value);
    }

    public function withHeader($name, $value)
    {
        $this->headers[$name] = (array)$value;
        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        $value = (array)$value;
        if ($this->hasHeader($name)) {
            $this->headers[$name] = array_merge($this->headers[$name], $value);
        } else {
            $this->withHeader($name, $value);
        }
        return $this;
    }

    public function withoutHeader($name)
    {
        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }
        return $this;
    }

    public function getBody()
    {
        // TODO: Implement getBody() method.
    }

    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

}