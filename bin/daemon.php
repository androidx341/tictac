<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS . '..' . DS . 'src'. DS);
define('VENDOR_DIR', ROOT .'..'. DS .'vendor' . DS);
define('CONF_DIR', ROOT . '..' . DS . 'config' . DS );
require VENDOR_DIR . 'autoload.php';

$config = Symfony\Component\Yaml\Yaml::parseFile(CONF_DIR . 'config.yml');
$parameters = $config['parameters'];
$dsn = "mysql: host={$parameters['database_host']}; dbname={$parameters['database_name']}";
$pdo = new \PDO($dsn, $parameters['database_user'], $parameters['database_password']);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$pdo->exec("set names utf8");

while(1) {
    $sql = 'DELETE FROM user_online
            WHERE time < DATE_SUB(NOW(), INTERVAL 1 MINUTE)';
    $sth = $pdo->prepare($sql);
    $sth->execute();
    sleep(1);
}