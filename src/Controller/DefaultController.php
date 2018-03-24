<?php
namespace Controller;

use Framework\Request;
use Framework\Session;
use Model\Entity\Game;
use Model\Entity\User;

class DefaultController extends \Framework\BaseController {

    public function indexAction(Request $request){

        $userId = Session::get('userId');
        /** @var User $user */
        /** @var Game $game */
        /** @var User $opponent */
        $user = $this->getRepository('User')->findById($userId);
        $game = $this->getRepository('Game')->find($user->getGame());
        $userStat = $this->getRepository('Game')->userStat($user->getId());
        Session::set('userStat',$userStat);
        if ($game){
            $this->getRepository('User')->removeGame($game->getId());
            $this->getRepository('Game')->disableGame($game->getId());
         }

        return  $this->render('index.html.twig',[

        ]);
    }


}