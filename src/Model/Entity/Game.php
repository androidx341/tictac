<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.12.2017
 * Time: 13:01
 */

namespace Model\Entity;


class Game
{
    private $id;
    private $isActive;
    private $user_win;
    private $game_stamp;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserWin()
    {
        return $this->user_win;
    }

    /**
     * @param mixed $user_win
     */
    public function setUserWin($user_win)
    {
        $this->user_win = $user_win;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGameStamp()
    {
        return $this->game_stamp;
    }

    /**
     * @param mixed $game_stamp
     */
    public function setGameStamp($game_stamp)
    {
        $this->game_stamp = $game_stamp;
        return $this;
    }





}