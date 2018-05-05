<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 14:57
 */

namespace Admin\Controller;


class AdminController extends BaseController
{

    public function index()
    {
        $list = D('Admin')->where('type!=1')->select();
        // $rlist = D('xcommunity_region')->select();
        // $rname = $this->return_all_name($rlist,'title');
        // // foreach($list as $k=>$v){
        //     if(!empty($v['region'])){
        //         $arr = explode(',',$v['region']);
        //         for($i=0 ; $i < 3 ; $i++){
        //             $list[$k]['rname'] .= empty($rname[$arr[$i]]) ? '' : $rname[$arr[$i]].',';
        //         }
        //     }
        // }
        $this->assign('list', $list);
        $this->display();
    }

    public function rbac()
    {
        if (IS_POST) {
            $id = I('post.id');
            foreach ($_POST['data'] as $item => $value) {
                $model = D('Model')->find(intval($value['id']));
                $arr = explode(',', $model['user_id']);
                if ($value['checked'] == 'true') {
                    if (!in_array($id, $arr)) {//当被选中，但是不存在的时候加入id进去
                        $data['user_id'] = trim($model['user_id'] . ',' . $id, ',');
                        $data['id'] = $value['id'];
                        D('Model')->save($data);
                    }
                } else {
                    if (in_array($id, $arr)) {//当没有选中，但是存在的时候去掉id
                        unset($arr[array_search($id, $arr)]);
                        $data['user_id'] = implode(',',$arr);
                        $data['id'] = $value['id'];
                        D('Model')->save($data);
                    }
                }
            }
            ReturnJson(1, 10006);
        } else {
            $str = '';
            $id = I('get.id');
            $list = D('Model')->where(" level!=0 AND controller !='Setting'")->select();
            foreach ($list as $key => $v) {
                $arr = explode(',', $v['user_id']);
                $checked = in_array($id, $arr) ? 'true' : 'false';
                $str .= "{'id':'" . $v['id'] . "','belongid':'" . $v['belongid'] . "','name':'" . $v['name'] . "','checked':'" . $checked . "','open':true,'user_id':'" . $v['user_id'] . "'},";
            }
            $this->assign('str', trim($str, ','));
            $this->display();
        }
    }

    public function log_view(){
        $count = D('admin_log')->count();
        $Page = new \Think\Page($count,15);
        $show = $Page->show();
        $list = D('admin_log')->where(1)->order("id DESC")->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['time'] = date("Y-m-d H:i:s",$v['time']);
        }
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display();
    }

    public function add(){
        if (IS_POST) {
            if(empty($_POST['username'])){
                ReturnJson(0,'아이디를 입력해주세요');
            }else{
                $uname = $_POST['username'];
                $count = D('admin')->where(" username = '$uname' ")->count();
                if($count > 0){
                    ReturnJson(0,'존재하는 아이디입니다');
                }
            }
            if(empty($_POST['nickname'])){
                ReturnJson(0,'닉네임을 입력해주세요');
            }
//            if(!(Validate::isNames($_POST['username'], 5, 20))){
//                ReturnJson(0,'账号长度必须在5—20位');
//            }
            if(empty($_POST['password1'])){
                ReturnJson(0,'비밀번호를 입력해주세요');
            }
            if(!(Validate::isPWD($_POST['password1'], 5, 20))){
                ReturnJson(0,'비밀번호는 최소5자 최대20자까지 입력해주세요');
            }
            if($_POST['password1'] != $_POST['password2'] ){
                ReturnJson(0,'비밀번호가 일치하지 않습니다');
            }

            $pwd = I('password1','','md5');

//            生成随机邀请码
            $code = date("YmdHsi").rand(1000,9999);;
            $data = array(
                'username'=>$_POST['username'],
                'nickname'=>$_POST['nickname'],
                'password'=>$pwd,
                'type'=> 2,
                'parent_id' => $_SESSION['admin']['id'],
                'code' => $code,
            );
            $res = D('admin')->add($data);

            if($res !== false){
                $this->set_admin_log('관리자계정——추가：'.$_POST['username']);
                ReturnJson(1, 10007);
            }else {
                ReturnJson(0, 10008);
            }
        }else{
            $this->display();
        }
    }

    public function save(){
        if (IS_POST) {
            if(empty($_POST['password1'])){
                ReturnJson(0,'비밀번호를 입력해주세요');
            }
            if(!(Validate::isPWD($_POST['password1'], 5, 20))){
                ReturnJson(0,'비밀번호는 최소5자 최대20자까지 입력해주세요');
            }
            if($_POST['password1'] != $_POST['password2'] ){
                ReturnJson(0,'비밀번호가 일치하지 않습니다');
            }
            $pwd = I('password1','','md5');
            $data = array(
                'id'=>$_POST['id'],
                'password'=>$pwd,
            );
            $res = D('admin')->save($data);

            if($res !== false){
                $this->set_admin_log('어드민——비번변경：'.$_POST['username']);
                ReturnJson(1, 10009);
            }else {
                ReturnJson(0, 10010);
            }
        }else{
            $admin = D('admin')->find(intval($_GET['id']));

            $this->assign('admin',$admin);
            $this->display();
        }
    }

    public function nickname(){
        if (IS_POST) {
            if(empty($_POST['nickname'])){
                ReturnJson(0,'닉네임을 입력해주세요');
            }
            $data = array(
                'id'=>$_POST['id'],
                'nickname'=>$_POST['nickname']
            );
            $res = D('admin')->save($data);
            $find = D('admin')->find($_POST['id']);
            if($res !== false){
                $this->set_admin_log('어드민—비번변경：'.$find['username']);
                ReturnJson(1, 10009);
            }else {
                ReturnJson(0, 10010);
            }
        }else{
            $admin = D('admin')->find(intval($_GET['id']));
//            $list = M('lab','erp_')->select();
//            $this->assign('list',$list);
            $this->assign('admin',$admin);
            $this->display();
        }
    }

    //删除
    public function del()
    {
        $id = I('post.id', '0', 'intval');
        $deluser = D('admin')->find($id);
        if (D('admin')->delete($id) === false) {
            ReturnJson(0, 10012);
        } else {
            $this->set_admin_log('어드민——삭제：'.$deluser['username']);
            ReturnJson(1, 10011);
        }
    }

    public function score_log(){
        $count = D('score_log')->count();
        $Page = new \Think\Page($count,15);
        $show = $Page->show();
        $list = D('score_log')->order("id DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k=>$v){
            $list[$k]['user'] = D('users')->find($v['user_id']);
        }
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display();
    }


}