<?php
/**
 * Created by 坚果.
 * User: zq
 * Date: 2017/10/28
 * Time: 12:49
 */

namespace Admin\Controller;
use Think\Controller;

class ApiController extends Controller
{

    public function getNew(){

        $new = M("wallet_log")->where(array('status' => 0))->select();

        if(empty($new)){
            return_ajax("no something");
        }
        return_ajax($new,1);



    }




}