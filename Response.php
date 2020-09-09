<?php


namespace Moon\Request;


use Psr\Http\Message\ResponseInterface;

class Response  extends Message implements ResponseInterface
{
    protected $statusCode;
    protected $reasonPhrase = '';

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $this->statusCode = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

}