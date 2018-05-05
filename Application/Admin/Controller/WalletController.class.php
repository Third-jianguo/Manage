<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class WalletController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '钱包管理';
        $GLOBALS['Template']['DBName'] = 'Wallet';
    }

    public function index(){
        global $Template;
        $Template['DBName'] = "Wallet w";
        $Template['SHOW_FIELD'] = "w.* , u.name user_name, curr.sn type_name";
        $Template['JOIN'] = array("btc_user u ON u.id=w.user_id", "btc_currency curr ON curr.id=w.wallet_type");
        if(!empty($_GET['use'])){
            if($_GET['use'] == 2){
                $Template['WHERE'] = array('w.user_id' => '0');
            }
            if($_GET['use'] == 1){
                $Template['WHERE'] = array('w.user_id' => array('neq','0'));
            }
        }

        if(!empty($_GET['currency'])){
            $Template['WHERE'] = array('w.wallet_type' => $_GET['currency']);
        }

        if(!empty($_GET['name'])){
            $Template['WHERE'] = array('u.name' => array("like", "%{$_GET['name']}%"));
        }

        $currency = M("currency")->select();
        parent::assign("currency", $currency);
        parent::index();
    }

    public function add(){
        if(!IS_POST){
            $wallet_type = M("Currency")->select();
            $this->assign("wallet_type", $wallet_type);
        }

        if(IS_POST){
            $address_arr = explode("\n", $_POST['address']);
            $check = '';
            foreach($address_arr as $key => $val){
                $res = M("Wallet")->add(array('wallet_type' => $_POST['wallet_type'], 'address' => $val));
                if(empty($res)){
                    $check = 1;
                }
            }
            if(empty($check)){
                ReturnJson(1, 10007);
            } else {
                ReturnJson(0, 10008);
            }
        }


        $this->display();
    }


}