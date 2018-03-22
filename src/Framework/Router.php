<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.12.2017
 * Time: 20:45
 */

namespace Framework;


class Router
{
    private $routes;

    private $currentRoute;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function redirect($to){
        $to = $this->generateUrl($to);
        header("Location: {$to}");
        die;
    }

    public function generateUrl($name, array $parameters = []){
        foreach ($this->routes as $rname => $route){
            if ($name == $rname){
                if($parameters) {
                    $param_name = (array_keys($route['parameters']));
                    foreach ($parameters as $key => $param) {
                        $route = str_replace("{{$param_name[$key]}}", "{$parameters[$key]}", $route);
                    }
                }
                return $route['pattern'];
            }
        }
        return '/';
    }

    public function match(Request $request){
        $uri = $request->getUri();
        $routes = $this->routes;

        foreach ($routes as $route) {

            $pattern = $route['pattern'];
            if (!empty($route['parameters'])) {

                foreach ($route['parameters'] as $name => $regex) {
                    $pattern = str_replace(
                        '{' . $name . '}',
                        '(' . $regex . ')',
                        $pattern);
                }
            }
            $pattern = '@^' . $pattern . '$@';
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                if(!empty($route['parameters'])){
                    $result = array_combine(
                        array_keys($route['parameters']),
                        $matches
                    );
                    $request->mergeGetWithArray($result);
                }
                $this->currentRoute = $route;
                $this->isAuth($route);
                return;
            }
        }
        throw new \Exception('Page not found', 404);
    }

    public function getCurentController(){
        return $this->getCurrentRouteAttrib('controller');
    }

    public function getCurentAction(){
        return $this->getCurrentRouteAttrib('action');
    }

    private function getCurrentRouteAttrib($key){
        if(!$this->currentRoute){
            return null;
        }
        return $this->currentRoute[$key];
    }

    private function isAuth($route){
        if (!Session::has('user') && $this->currentRoute['pattern'] == '/'){
            $this->redirect('auth');
        }
    }
}