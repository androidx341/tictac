<?php
namespace Framework;

class Request
{
    private $post;
    private $get;
    private $server;

    public function __construct(array $get = [], array $post = [], array $server = [])
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
    }

    public function post($key, $default = null)
    {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }

        return $default;
    }

    public function get($key,$default = null)
    {
        if (isset($this->get[$key])) {
            return $this->get[$key];
        }

        return $default;
    }

    public function server($key)
    {
        if (isset($this->server[$key])) {
            return $this->server[$key];
        }

        return null;
    }

    public function isPost()
    {
        return (bool) $this->post;
    }

    public function getUri(){
        $uri = $this->server('REQUEST_URI');
        $uri = explode('?',$uri);
        return $uri[0];
    }

    public function mergeGetWithArray(array $arr)
     {
         $_GET += $arr;
         $this->get += $arr;
     }
}