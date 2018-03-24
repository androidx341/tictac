<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.12.2017
 * Time: 21:33
 */

namespace Framework\Twig;
use Framework\Router;
use Framework\Session;

class AppExtension extends \Twig_Extension
{
    private $router;

    public function __construct(Router $router){
        $this->router = $router;
    }

    public function getFunctions(){
        return [
            new \Twig_SimpleFunction('path', [$this, 'getUri']),
            new \Twig_SimpleFunction('sessionGet', [$this, 'sessionGet']),
            new \Twig_SimpleFunction('getFlash', [$this, 'getFlash'])
            ]
        ;
    }

    public function getUri($name, array $parameters = []){
        return $this->router->generateUrl($name, $parameters);
    }

    public function sessionGet($key){
        return Session::get($key);
    }

    public function getFlash(){
        return Session::getFlash();
    }
}