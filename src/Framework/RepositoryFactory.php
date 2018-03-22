<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 13.12.2017
 * Time: 15:14
 */

namespace Framework;


class RepositoryFactory
{
    /**
     * @var $pdo \PDO
     */
    protected $pdo;
    protected $repositories = [];

    public function setPDO(\PDO $pdo){
        $this->pdo = $pdo;
        return $this;
    }

    public function createRepository($r_name){
        if (isset($this->repositories[$r_name])){
            return $this->repositories[$r_name];
        }
        $classname = "\\Model\\Repository\\{$r_name}Repository";
        if (!class_exists($classname)) {
            throw new \Exception("Controller {$classname} not found");
        }
        $repository = new $classname;
        $repository->setPdo($this->pdo);
        $this->repositories[$r_name] = $repository;
        return $repository;
    }

}