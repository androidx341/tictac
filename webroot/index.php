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
    $parameters = $config['parameters'];
    $routing = $config['routing'];

    $dsn = "mysql: host={$parameters['database_host']}; dbname={$parameters['database_name']}";
    $dbConnection = new \PDO($dsn, $parameters['database_user'], $parameters['database_password']);
    $dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $dbConnection->exec("set names utf8");
    \Framework\Session::start();
    $loader = new Twig_Loader_Filesystem(VIEW_DIR);
    $twig = new Twig_Environment($loader);
    $request = new \Framework\Request($_GET, $_POST, $_SERVER);
    $router = new \Framework\Router($routing);
    $response = new \Framework\Response();
    $repositoryFactory = new \Framework\RepositoryFactory();
    $repositoryFactory->setPDO($dbConnection);
    $container = (new \Framework\Container())
        ->set('repository_factory', $repositoryFactory)
        ->set('router',$router)
        ->set('pdo', $dbConnection)
        ->set('response',$response)
        ->set('twig',$twig);
    $twig->addExtension(new \Framework\Twig\AppExtension($router));
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
       dump($e);
       echo $e->getMessage();
}
echo $content;
