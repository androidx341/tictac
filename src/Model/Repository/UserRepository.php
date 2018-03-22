<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.12.2017
 * Time: 13:03
 */

namespace Model\Repository;
use Model\Entity\User;

class UserRepository
{
    /**
     * @var \PDO
     */

    protected $pdo;
    public function setPdo(\PDO $pdo){
        $this->pdo = $pdo;
    }

    public function findByName($name){

        $sth = $this->pdo->prepare('select * from user WHERE name = :name');
        $sth->execute(['name' => $name]);
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        if (!$res) return null;
        return  $user = (new User())
            ->setId($res['id'])
            ->setUsername($res['name'])
            ->setPassword($res['password']);
    }

    public function addUser(User $user){
        $sql = 'INSERT INTO `user` (`id`, `name`, `password`) VALUES (NULL, :name, :password)';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'name' => $user->getUsername(),
            'password' => $user->getPassword(),
        ]);
        return $res;
    }
}