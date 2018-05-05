<?php
namespace Home\Model;

use Think\Model\RelationModel;


class UserModel extends RelationModel
{
    public function login(){
        if (!(check_verify($_POST['code']))) {
            return_ajax("10001");
        }

        if ($_SESSION['csrf'] != $_POST['csrf']) {
            return_ajax("csrf");
        }
        $where = array(
            'login_pwd' => md5($_POST['password'])
        );

        if (!empty($_POST['email'])) {
            $where['email'] = $_POST['email'];
        }
        if (!empty($_POST['mobile'])) {
            $where['tel'] = $_POST['mobile'];
            $where['tel_country'] = $_POST['tel_country'];
        }

        $checkLogin = M("User")->where($where)->find();
        if (empty($checkLogin)) {
            return_ajax("10000");
        }

        if ($checkLogin && $checkLogin['status'] == 0) {
            if(empty($checkLogin['google'])){
                //登录成功
                $_SESSION['user'] = $checkLogin;

                //add login log
                M("AdminLog")->add(array('admin_id' => $_SESSION['user']['id'], 'admin_name' =>
                    $_SESSION['user']['name'], 'time' => time(), 'login_ip' => get_client_ip(), 'details' => '登录' ,
                    'type' => 1));
                //分配钱包
                $this->giveWallet($_SESSION['user']['id']);
                return_ajax("10002", 1); //ok
            }else{
                return_ajax("check google",3, array('id' => $checkLogin['id']));
            }


        }
        return_ajax("10000"); //登录失败
    }

    public function register(){
        $name = trim(I("post.name"));
        $pwd = trim(I("post.password"));
        if ($_SESSION['csrf'] != $_POST['csrf']) {
            return_ajax("csrf");
        }
        if(4 > strlen($name) || strlen($name) > 12){
            return_ajax(10047);//格式
        }
        if( 6 > strlen($pwd) || strlen($pwd) > 16){
            return_ajax('密码长度不符合要求');//格式
        }

        $check = M("User")->where(array('tel' => $_POST['tel'], 'tel_country' => $_POST['tel_country']))->find();
        if($check){
            return_ajax("10022");//存在
        }

        $data = array(
            'name' => $_POST['name'],
            'login_pwd' => md5($_POST['password']),
            'tel' => $_POST['tel'],
            'tel_country' => $_POST['tel_country'],
            'ip' => get_client_ip(),
            'createtime' => time(),
        );
        return M("user")->add($data);
    }

    public function giveWallet( $uid ){
        $curr = M("Currency")->where(array('status' => 0))->select();
        $Model = M("Wallet");
        foreach($curr as $k => $v){
            $check = $Model->where(array('user_id' => $uid, 'wallet_type' => $v['id']))->find();
            if(empty($check)){
                $Model->where(array('wallet_type' => $v['id'], 'user_id' => array('eq',0)))->limit(1)->save(array
                ('user_id' =>
                $uid));
            }
        }
    }
}