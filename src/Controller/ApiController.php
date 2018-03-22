<?php
namespace Controller;

use Framework\Request;

class ApiController extends \Framework\BaseController {

    public function clickAction(Request $request){
        if ($request->isPost()){
            $cell = $request->post('cell');
            $row = $request->post('row');

            return $this->sendResponse()->jsonCodeMessageResponse(200,'ok');
        }
        return $this->sendResponse()->jsonCodeMessageResponse(404,'No post sended');
    }
}