<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 14.12.2017
 * Time: 13:11
 */

namespace Model\Form;

class RegisterForm
{

    public $username;
    public $password;
    public $password_second;

    /**
     * LoginForm constructor.
     * @param $email
     * @param $password
     * @param $password_second
     */
    public function __construct($username, $password, $password_second )
    {
        $this->username = $username;
        $this->password = $password;
        $this->password_second = $password_second;
    }

    public function isValid(){
        return
            !empty($this->username) &&
            !empty($this->password) &&
            !empty($this->password);

    }
    public function passCorrect(){
        return ($this->password == $this->password_second);
    }

}