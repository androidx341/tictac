<?php
try {
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', __DIR__ . DS . '..' . DS . 'src'. DS);
    define('VIEW_DIR', ROOT . 'View' . DS);
    define('VENDOR_DIR', ROOT .'..'. DS .'vendor' . DS);
    define('CONF_DIR', ROOT . '..' . DS . 'config' . DS );
    define('WEBROOT',__DIR__.DS);
    require VENDOR_DIR . 'autoload.php';

    $config = Symfony\Component\Yaml\Yaml::parseFile(CONF_DIR . 'config.yml');
    $routing = $config['routing'];

    $loader = new Twig_Loader_Filesystem(VIEW_DIR);
    $twig = new Twig_Environment($loader);
    $request = new \Framework\Request($_GET, $_POST, $_SERVER);
    $router = new \Framework\Router($routing);
    $container = (new \Framework\Container())
        ->set('twig',$twig);
}
catch (\Exception $e)
{
   echo $e->getMessage();
}

try {
    $router->match($request);
    $controller = '\\Controller\\' . $router->getCurentController();
    $action = $router->getCurentAction();
    if (!class_exists($controller)) {
        throw new \Exception("Controller {$controller} not found");
    }
    $controller = (new $controller())->setContainer($container);
    if (!method_exists($controller, $action)) {
        throw new \Exception("No such method {$action}");
    }
    $content = $controller->$action($request);
}
 catch (\Exception $e) {
       echo $e->getMessage();
}
echo $content;
