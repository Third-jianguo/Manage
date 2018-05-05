<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class UserController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '用户管理';
        $GLOBALS['Template']['DBName'] = 'User';

    }

    public function index(){
        if(!empty($_GET)){
            global $Template;
            $Template['WHERE'] = " 1 ";
            $key = $_GET['keyword'];
            if(!empty($key)){
                $Template['WHERE'] .= " AND ( name like '%{$key}%' or  tel like '%{$key}%' or  id_name like '%{$key}%' or  id_card like '%{$key}%' ) ";
            }
            if($_GET['status'] != 999){
                $Template['WHERE'] .= " AND status = '{$_GET[status]}' ";
            }
            parent::assign("search", $_REQUEST);
        }
        parent::index();
    }

    public function black(){
        global $Template;
        $id = I('post.id', '0', 'intval');
        $del = M("User")->find($id);
        $res1 = D("User")->save(array('id' => $id, 'status' => 1));
        if ($res1 === false) {
            ReturnJson(0, "no");
        } else {
            $this->set_admin_log($Template['CONTROLLER_NAME'] . '—加入黑名单：' . $del['name']);
            ReturnJson(1, "ok");
        }
    }
    public function returnBlack(){
        global $Template;
        $id = I('post.id', '0', 'intval');
        $del = M("User")->find($id);
        $res1 = D("User")->save(array('id' => $id, 'status' => 0));
        if ($res1 === false) {
            ReturnJson(0, "no");
        } else {
            $this->set_admin_log($Template['CONTROLLER_NAME'] . '—移出黑名单：' . $del['name']);
            ReturnJson(1, "ok");
        }
    }

    public function userMoney(){


        $this->display();
    }

    public function add(){
        global $Template;
        if(!IS_POST){
            $country = M("static_tel")->select();
            parent::assign("tel", $country);
        }
        parent::add();
    }


    public function save(){
        global $Template;
        if(IS_POST){
            $post = $_POST;
            if($post['login_pwd'] == ''){
                unset($post['login_pwd']);
            }else{
                $post['login_pwd'] = md5($post['login_pwd']);
            }
            if($post['pay_pwd'] == ''){
                unset($post['pay_pwd']);
            }else{
                $post['pay_pwd'] = md5($post['pay_pwd']);
            }
            if(empty($post['tel_country'])){
                unset($post['tel_country']);
            }
            $res = M("User")->save($post);
            $name = M("User")->where(array('id' => $post['id']))->getField("name");
            if ($res !== false) {
                $this->set_admin_log($Template['CONTROLLER_NAME'] . '—修改id:'.$post['id'].' '.$name.'密码：' );
                ReturnJson(1, 10009);
            } else {
                ReturnJson(0, 10010);
            }


        }
        $currency = M("currency")->select();
        $list = M("User")->where(array('id' => $_GET['id']))->select();

        foreach($list as $key => $val){
            $wallet = M("Wallet")->where(array('user_id' => $val['id']))->select();
            $wallet_arr = array();
            foreach($wallet as $wk => $wv){
                $wallet_arr[$wv['wallet_type']] = $wv;  //arr[货币类型][info]
                $wallet_arr[$wv['wallet_type']]['all'] = $wv['money'] + $wv['stock_money'];  //arr[货币类型][info]
            }

            $list[$key]['wallet'] = $wallet_arr;
        }
        $country = M("static_tel")->select();
        $this->assign("tel", $country);
        $info = M("User")->find($_GET['id']);
        $this->assign("currency", $currency);
        $this->assign("list", $list);
        $this->assign("info", $info);

        $this->display();
    }

    public function tixian(){
        $where = array();
        $log_type = array('6' => '线上转出','10' => '交易买入', '20' => '交易卖出');

        $where['log.log_type'] = 6;



        $count = M("WalletLog log")->field("log.*, u.name")
            ->join("btc_user u ON log.user_id = u.id", 'left')
            ->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();




        $list = M("WalletLog log")->field("log.*, u.name")
            ->join("btc_user u ON log.user_id = u.id", 'left')
            ->where($where)->order('id desc, status desc');

        if(!empty($_GET['page'])){
            $list = $list->limit($_GET['page'])->select();
        }else{
            $list = $list->limit($Page->firstRow.','.$Page->listRows)->select();
        }

        foreach($list as $key => $val){
            $list[$key]['shiji'] = $val['number'] - $val['service_money'];
            $list[$key]['type'] = $log_type[$val['log_type']];
        }
//        echo M("WalletLog log")->getLastSql();
        $this->assign("currency", M("currency")->select());
        $this->assign('page', $show);
        $this->assign("list", $list);
        $this->display();
    }

    public function zhuanzhangok(){
        $id = $_REQUEST['id'];
        if(empty($id)){exit;};
        $log = M("wallet_log")->find($id);
        $wallet = M("wallet")->find($log['wallet_id']);
        if($log['number'] > $wallet['money']){
            ReturnJson(0, "异常:余额不足");
        }

        $model = M();
        $model->startTrans();
        $res = M("wallet_log")->save(array('id' => $id, 'status' => 1, 'msg' => $log['msg']."<br/>管理员已转账"));
        $res2 = M("wallet")->where(array('id' => $log['wallet_id']))->setDec("stock_money", $log['number']);

        if(empty($res) || empty($res2)){ $model->rollback();return_ajax("no");}

        $model->commit();
        ReturnJson(1,"ok");
    }

    public function zhuanzhangcancel(){
        $id = $_REQUEST['id'];
        $log = M("wallet_log")->find($id);

        $res = M("wallet_log")->save(array('id' => $id, 'status' => 99, 'msg' => $log['msg']."<br/>管理员取消"));

        //todo 取消后 冻结的资金如何处理



    }

}