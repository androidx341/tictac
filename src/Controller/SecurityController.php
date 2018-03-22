<?php
namespace Controller;

use Framework\Request;
use Framework\Session;
use Model\Entity\User;
use Model\Form\LoginForm;
use Model\Form\RegisterForm;

class SecurityController extends \Framework\BaseController {

    public function authAction(Request $request){
        if (Session::get('user'))
        {
            $this->getRouter()->redirect('homepage');
        }
        return  $this->render('login.html.twig');
    }
    public function loginAction(Request $request){
        $form = new LoginForm(
            $request->post('userName'),
            $request->post('userPassword')
        );
        if($request->isPost()){
            if($form->isValid()){
                /**
                 * @var $user User
                 */
                $user = $this->getRepository('User')->findByName($form->username);

                if (!$user) return $this->sendResponse()->jsonCodeMessageResponse(404,'User not found');
                if (password_verify($form->password,$user->getPassword())){
                    Session::set('user',$user->getUsername());
                    Session::set('userId',$user->getId());
                    return $this->sendResponse()->jsonCodeMessageResponse(200,'Login Ok');
                }
                return $this->sendResponse()->jsonCodeMessageResponse(500,'Password incorect');
            }
        }
        return $this->sendResponse()->jsonCodeMessageResponse(500,'No post sended');
    }

    public function registerAction(Request $request){
        $form = new RegisterForm(
            $request->post('userName'),
            $request->post('userPassword'),
            $request->post('userCheckPassword')
        );
        if ($form->isValid()){
            if ($form->isValid()){
                if (!$form->passCorrect())
                    return $this->sendResponse()->jsonCodeMessageResponse(500,'Passwords are not equal');
                /**
                 * @var $user User
                 */
                $find_user = $this->getRepository('User')->findByName($form->username);
                if ($find_user)  return $this->sendResponse()->jsonCodeMessageResponse(500,'User alredy exist');
                $new_user = (new User())
                    ->setUsername($form->username)
                    ->setPassword(password_hash($form->password,PASSWORD_DEFAULT));
                $res = $this->getRepository('User')->addUser($new_user);

                if ($res)  return $this->sendResponse()->jsonCodeMessageResponse(200,'Registration complete');
            }
        }
        return $this->sendResponse()->jsonCodeMessageResponse(404,'Form does not sended');
    }
    public function logoutAction(Request $request){
        Session::remove('user');
        $this->getRouter()->redirect('auth');
    }
}