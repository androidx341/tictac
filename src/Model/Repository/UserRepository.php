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
            ->setPassword($res['password'])
            ->setTime($res['last_online'])
            ->setGame($res['current_game']);
    }
    public function findById($id){

        $sth = $this->pdo->prepare('select * from user WHERE id = :id');
        $sth->execute(['id' => $id]);
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        if (!$res) return null;
        return  $user = (new User())
            ->setId($res['id'])
            ->setUsername($res['name'])
            ->setPassword($res['password'])
            ->setTime($res['last_online'])
            ->setGame($res['current_game']);
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

    public function setOnline($userId){
        $sql = 'UPDATE `user` SET last_online = CURRENT_TIMESTAMP 
                WHERE id = :userId';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'userId' => $userId,
        ]);
        return $res;
    }

    public function setGame($userId,$gameId){
        $sql = 'UPDATE `user` SET current_game = :gameId 
                WHERE id = :userId';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'gameId' => $gameId,
            'userId' => $userId,
        ]);
        return $res;
    }

    public function removeGame($gameId){
        $sql = 'UPDATE `user` SET current_game = NULL 
                WHERE current_game = :gameId';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'gameId' => $gameId,
        ]);
        return $res;
    }
}