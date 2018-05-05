<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {

    public function __construct()
    {
        parent::__construct();

        parent::assign("user", $_SESSION['user']);
    }

    public function checkLogin(){
        if(empty($_SESSION['user']['id'])){
            return "out";
        }
        return "ok";
    }

}