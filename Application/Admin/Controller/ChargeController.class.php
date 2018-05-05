<?php
/**
 * Created by 坚果.
 * User: zq
 * 交易手续费
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class ChargeController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '手续费管理';
        $GLOBALS['Template']['DBName'] = 'Charge';
    }

    public function index(){
        parent::index("save");
    }

    public function save(){

        M("charge")->save(array('id' => 1, 'val' => $_POST['val']));
        $this->set_admin_log('交易手续费修改为'.$_POST['val']);
        return_ajax("10009",1);
    }
}