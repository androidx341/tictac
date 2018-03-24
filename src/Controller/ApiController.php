<?php
namespace Controller;

use Framework\Request;
use Framework\Session;
use Model\Entity\Game;
use Model\Entity\User;

class ApiController extends \Framework\BaseController {

    public function clickAction(Request $request){
        if ($request->isPost()){
            $userId = Session::get('userId');
            /** @var User $user */
            /** @var Game $game */
            /** @var User $opponent */
            $user = $this->getRepository('User')->findById($userId);
            $game = $this->getRepository('Game')->find($user->getGame());
            $opponent = $this->getRepository('Game')->findOpponent($userId,$game->getId());
            if(is_null($game)){
                return $this->sendResponse()->jsonCodeMessageResponse(200,'Game Not Found');
            }
            $gameStamp = unserialize($game->getGameStamp());
            $next = $gameStamp['next'];
            $field = $gameStamp['field'];
            $step = $gameStamp['step'];
            if ($next != $user->getId()){
                return $this->sendResponse()->jsonCodeMessageResponse(200,'Not your move');
            }
            $col = $request->post('col');
            $row = $request->post('row');
            if ($field[$row][$col] != 0){
                return $this->sendResponse()->jsonCodeMessageResponse(200,'Cell not empty');
            }

            if($step % 2 == 1){
                $sign = 1;
                $field[$row][$col] = $sign;
            }else{
                $sign = 2;
                $field[$row][$col] = $sign;
            }

            $step+=1;
            $result = $this->checkWin($field);
            $gameStamp = [
                'next' => $opponent->getId(),
                'field' => $field,
                'step' => $step,
                'result' => $result
            ];
            $this->getRepository('Game')->updateGameStamp($game->getId(),serialize($gameStamp));
            if ($result){
                $this->getRepository('Game')->setWinGame($game->getId(),$userId);
            }

            return $this->sendResponse()->jsonCodeMessageResponse(200,[
                'step' => $step,
                'sign' => $sign
            ]);
        }

        return $this->sendResponse()->jsonCodeMessageResponse(500,'No post sended');
    }

    public function onlineAction(Request $request){
        $userId = Session::get('userId');
        /** @var User $user */
        $user = $this->getRepository('User')->findById($userId);
        if ($request->isPost()){
           $this->getRepository('User')->setOnline($userId);
           if(is_null($user->getGame())){
                $gameId = $this
                    ->getRepository('Game')
                    ->findFreeGame();
                if (!$gameId){
                    $gameStamp = serialize($this->initGame($userId));
                    $gameId = $this
                        ->getRepository('Game')
                        ->createGame($gameStamp);
                    $this
                        ->getRepository('Game')
                        ->joinGame($userId,$gameId);
                }else{
                    $this
                        ->getRepository('Game')
                        ->joinGame($userId,$gameId);
                }
                $this->getRepository('User')->setGame($userId,$gameId);
            }
            $gameId = $user->getGame();
            $opponent = $this->getRepository('Game')->findOpponent($userId,$gameId);
            if (!$opponent){
            return $this->sendResponse()->jsonCodeMessageResponse(200,[
                'clear' => 1,
                'op_name'=> 'Ожидание'
            ]);
            }
           $opponent_name = $opponent->getUsername();
            /** @var Game $game */
           $game = $this->getRepository('Game')->find($user->getGame());
           $field = unserialize($game->getGameStamp())['field'];
           $result = unserialize($game->getGameStamp())['result'];
           $step = unserialize($game->getGameStamp())['step'];
           $next = unserialize($game->getGameStamp())['next'];
           $message = null;
           if($result){
               $message = $this
                       ->getRepository('User')
                       ->findById($game->getUserWin())
                   ->getUsername();
               $message = 'Победил игрок: '.$message;
           } elseif ($next != $user->getId()){
                $message = 'Ожидание хода противника';
            } else $message = 'Ваш ход';

           if ($step == 10){
                $message = 'Ничья';
                $result = 1;
           }

           return $this->sendResponse()->jsonCodeMessageResponse(200,[
               'clear' => 0,
               'result' => $result,
               'field' => $field,
               'op_name'=> $opponent_name,
               'message' => $message
           ]);
        }
        return $this->sendResponse()->jsonCodeMessageResponse(500,'No post sended');
    }

    public function initGame($playerId)
    {
        $gameField = [[0,0,0],[0,0,0],[0,0,0]];
        return [
            'step' => 1,
            'next' => $playerId,
            'field' => $gameField,
            'result' => NULL
        ];
    }

    public function checkWin($field){
        //Horizontal test
        $size = count($field);
        for ($i = 0; $i < $size;$i++){
            $one = $two = 0;
            for ($j = 0; $j < $size;$j++){
                if($field[$i][$j] == 1){
                    $one+=1;
                }
                if($field[$i][$j] == 2){
                    $two+=1;
                }
            }
            if ($one == 3 || $two == 3 ){
                return [
                    'win' => ($one>$two)?1:2,
                    'line' => $i,
                    'lineType' => 'h'
                ];
            }
        }
        //Vertical test
        for ($i = 0; $i < $size;$i++){
            $one = $two = 0;
            for ($j = 0; $j < $size;$j++){
                if($field[$j][$i] == 1){
                    $one+=1;
                }
                if($field[$j][$i] == 2){
                    $two+=1;
                }
            }
            if ($one == 3 || $two == 3 ){
                return [
                    'win' => ($one>$two)?1:2,
                    'line' => $i,
                    'lineType' => 'v'
                ];
            }
        }
        //Diagonal test
        $oneA = $twoA = 0;
        $oneB = $twoB = 0;
        for ($i = 0; $i < $size;$i++){
            if($field[$i][$i] == 1){
                $oneA+=1;
            }
            if($field[$i][$i] == 2){
                $twoA+=1;
            }
            if($field[$i][($size-$i)-1] == 1){
                $oneB+=1;
            }
            if($field[$i][($size-$i)-1] == 2){
                $twoB+=1;
            }
            if ($oneA == 3 || $twoA == 3 ){
                return [
                    'win' => ($oneA>$twoA)?1:2,
                    'line' => 1,
                    'lineType' => 'd'
                ];
            }
            if ($oneB == 3 || $twoB == 3 ){
                dump($oneB);
                return [
                    'win' => ($oneB>$twoB)?1:2,
                    'line' => 2,
                    'lineType' => 'd'
                ];
            }
        }
    }
}