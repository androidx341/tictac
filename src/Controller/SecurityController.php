<?php
namespace Controller;

use Framework\Request;

class SecurityController extends \Framework\BaseController {

    public function loginAction(Request $request){

//        $form = new LoginForm(
//            $request->post('userEmail'),
//            $request->post('userPassword')
//        );
//        if($request->isPost()){
//
//            if($form->isValid()){
//                /**
//                 * @var $user User
//                 */
//                $user = $this->getRepository('User')->findByEmail($form->email);
//                if (!$user) return $this->sendResponse()->jsonCodeMessageResponse(404,'User not found');
//                if (password_verify($form->password,$user->getPassword())){
//
//
//                    switch ($user->getRole()){
//                        case 1:  Session::set('user',$user->getEmail());
//                            Session::set('userId',$user->getId());
//                            return $this->sendResponse()->jsonCodeMessageResponse(200,'You are user');
//                            break;
//                        case 2:  Session::set('admin','true');
//                            Session::set('user',$user->getEmail());
//                            Session::set('userId',$user->getId());
//                            return $this->sendResponse()->jsonCodeMessageResponse(200,'You are admin');
//                            break;
//                        case 3: Session::set('manager',$user->getEmail()); break;
//                    }
//                }
//                return $this->sendResponse()->jsonCodeMessageResponse(404,'Password incorect');
//            }
//        }
//        return $this->sendResponse()->jsonCodeMessageResponse(404,'No post sended');
        return  $this->render('login.html.twig');
    }
}