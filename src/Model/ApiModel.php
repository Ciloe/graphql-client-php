<?php

namespace GraphClientPhp\Model;

final class ApiModel
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $token;

    /**
     * @param string $host
     * @param string $uri
     * @param string $token
     */
    public function __construct(
        string $host = null,
        string $uri = null,
        string $token = null
    ) {
        $this->host = $host;
        $this->uri = $uri;
        $this->token = $token;
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return self
     */
    public function setHost(string $host): ApiModel
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return self
     */
    public function setUri(string $uri): ApiModel
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return self
     */
    public function setToken(string $token): ApiModel
    {
        $this->token = $token;

        return $this;
    }
}
