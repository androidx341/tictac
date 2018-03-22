<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 14.12.2017
 * Time: 13:11
 */

namespace Model\Form;

class LoginForm
{

    public $username;
    public $password;

    /**
     * LoginForm constructor.
     * @param $email
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function isValid(){
        return
            !empty($this->username) &&
            !empty($this->password);

    }

}