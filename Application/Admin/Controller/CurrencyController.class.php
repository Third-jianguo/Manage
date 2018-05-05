<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class CurrencyController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '可交易货币种类';
        $GLOBALS['Template']['DBName'] = 'Currency';
    }

//    public function index(){
//        parent::index("save");
//    }


    public function add(){
        global $Template;
        if(IS_POST){
            $this->set_admin_log($Template['CONTROLLER_NAME'] . '—添加：' . $_POST['name']."手续费:".$_POST['service']);
        }
        parent::add();
    }

}