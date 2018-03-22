<?php
namespace Controller;

use Framework\Request;
use Framework\Session;

class DefaultController extends \Framework\BaseController {

    public function indexAction(Request $request){
        return  $this->render('index.html.twig');
    }
}