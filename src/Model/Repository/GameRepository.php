<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.12.2017
 * Time: 13:03
 */

namespace Model\Repository;

use Model\Entity\Game;
use Model\Entity\User;

class GameRepository
{
    /**
     * @var \PDO
     */

    protected $pdo;
    public function setPdo(\PDO $pdo){
        $this->pdo = $pdo;
    }

    public function findOpponent($userId, $gameId){
        $sql = 'SELECT `id`, `name` FROM user
                WHERE current_game = :gameId AND id <> :userId';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            'gameId' => $gameId,
            'userId' => $userId
        ]);
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        if (!$res) return null;

        return  $user = (new User())
            ->setId($res['id'])
            ->setUsername($res['name']);
    }

    public function findFreeGame(){
        $sql = 'SELECT COUNT(ug.user_id) as user_count, g.id, g.isActive FROM game g
                JOIN user_game ug ON ug.game_id = g.id
                WHERE g.isActive = 1
                GROUP BY g.id
                HAVING user_count = 1
                LIMIT 1';
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        if (!$res) return null;
        return $gameId = $res['id'];
    }

    public function find($id){
        $sql = 'SELECT * FROM game
                WHERE game.id = :id';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            'id' => $id
        ]);
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        if (!$res) return null;
        return  $game = (new Game())
            ->setId($res['id'])
            ->setIsActive($res['isActive'])
            ->setUserWin($res['user_win'])
            ->setGameStamp($res['game_stamp']);
    }

    public function userStat($user){
        $sql = 'SELECT COUNT(*) as win_count FROM game
                WHERE user_win = :user';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            'user' => $user
        ]);
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        if (!$res) return null;
        return $res['win_count'];
    }

    public function updateGameStamp($id,$gameStamp){
        $sql = 'UPDATE `game` SET game_stamp = :gameStamp
                WHERE id = :id';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'id' => $id,
            'gameStamp' => $gameStamp
        ]);
        return $res;
    }

    public function disableGame($id){
        $sql = 'UPDATE `game` SET isActive = 0
                WHERE id = :id';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'id' => $id,
        ]);
        return $res;
    }

    public function setWinGame($id,$userId){
        $sql = 'UPDATE `game` SET isActive = 0, user_win = :userId
                WHERE id = :id';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'id' => $id,
            'userId' => $userId
        ]);
        return $res;
    }


    public function createGame($game){
        $sql = 'INSERT INTO `game` (`id`, `isActive`, `user_win`, `game_stamp`) VALUES (NULL, 1, NULL, :game)';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'game' => $game
        ]);
        $last_id = $this->pdo->lastInsertId();
        return $last_id;
    }

    public function joinGame($userId,$gameId){
        $sql = 'INSERT INTO `user_game` (`user_id`, `game_id`) VALUES (:userId, :gameId)';
        $sth = $this->pdo->prepare($sql);
        $res = $sth->execute([
            'userId' => $userId,
            'gameId' => $gameId
        ]);
        return $res;
    }
}