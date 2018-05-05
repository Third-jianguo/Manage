<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 14:57
 */

namespace Admin\Controller;


class CategoryController extends BaseController
{

    public function index(){
        $where = " 1 ";
        if(!empty($_REQUEST['name'])){
            $name = $_REQUEST['name'];
            $where .= " AND name LIKE '%$name%' ";
        }
        $list = D('Category')->where($where)->order(' display ASC')->select();

        $this->assign('list', $this->tree($list));
        $this->assign('search',$_REQUEST);
        $this->display();
    }

    public function add(){
        if (IS_POST) {
            $_POST['name'] = trim($_POST['name']);
            if(empty($_POST['name']))ReturnJson(0,'分类名称不能为空');


            $data = array(
                'display'=>empty($_POST['display'])?100:$_POST['display'],
                'url' => $_POST['url'],
                'rank' => $_POST['rank'] + 1,
                'image' => $_POST['image'],
                'name'=>$_POST['name'],
            );
            if(!empty($_POST['parent'])){
                $parentId = M("Category")->where(array('id' => $_POST['parent']))->getField("id");

                $data['parent'] = $parentId;
            }
            $res = D('Category')->add($data);
            if($res !== false){
                $this->set_admin_log('分类管理—添加：'.$_POST['name']);
                ReturnJson(1, 10007);
            }else {
                ReturnJson(0, 10008);
            }
        }
        $parentName = M("Category")->where(array('id' => $_GET['id']))->find();

        $this->assign('parentName', $parentName);
        $this->display();
    }

    public function save(){
        if (IS_POST) {
            $_POST['name'] = trim($_POST['name']);
            if(empty($_POST['name']))ReturnJson(0,'分类名称不能为空');

            $data = array(
                'id'=>$_POST['id'],
                'url' => $_POST['url'],
                'image' => $_POST['image'],
                'display'=>$_POST['display'],
                'name'=>$_POST['name'],
            );
            $res = D('Category')->save($data);
            if($res !== false){
                $this->set_admin_log('分类管理—修改：'.$_POST['name']);
                ReturnJson(1, 10009);
            }else {
                ReturnJson(0, 10010);
            }
        }else{
            $cons = D('Category')->find(intval($_GET['id']));
            $this->assign('cons',$cons);
            $this->display();
        }
    }
//
//    public function addlower(){
//        if (IS_POST) {
//            $_POST['name'] = trim($_POST['name']);
//            if(empty($_POST['name']))ReturnJson(0,'分类名称不能为空');
//            if(empty($_POST['region_id']))ReturnJson(0,'所属小区不能为空');
//            $data = array(
//                'displayorder'=>$_POST['displayorder'],
//                'name'=>$_POST['name'],
//                'region_id'=>implode(',',$_POST['region_id']),
//                'description'=>$_POST['description'],
//                'parentid'=>$_POST['parentid'],
//                'enabled'=>1,
//            );
//            $res = D('xcommunity_servicecategory')->add($data);
//            if($res !== false){
//                $this->set_admin_log('分类管理—添加子分类：'.$_POST['title']);
//                ReturnJson(1, 10007);
//            }else {
//                ReturnJson(0, 10008);
//            }
//        }else{
//            $cons = D('xcommunity_servicecategory')->find(intval($_GET['id']));
//            $rlist = $this->get_regions();
//            $this->assign('rlist', $rlist);
//            $this->assign('cons',$cons);
//            $this->display();
//        }
//    }

    //删除
    public function del(){
        $id = I('post.id', '0', 'intval');
        $del = D('Category')->find($id);
        $data = array(
            'id' => $id,
            'status' => 1
        );
        $res = D('Category')->save($data);
        if ($res === false ) {
            ReturnJson(0, 10012);
        } else {
            $this->set_admin_log('分类管理—删除：'.$del['name']);
            ReturnJson(1, 10011);
        }
    }

    //恢复
    public function getback(){
        $id = I('post.id', '0', 'intval');
        $del = D('Category')->find($id);
        $data = array(
            'id' => $id,
            'status' => 0
        );
        $res = D('Category')->save($data);
        if ($res === false ) {
            ReturnJson(0, '恢复失败，请重试！');
        } else {
            $this->set_admin_log('分类管理—恢复：'.$del['name']);
            ReturnJson(1, '恢复成功！');
        }
    }

    public function tree($array, $parent = 0, $number = 0){
        static $arr = array();
        static $f = "├──";
        if (is_array($array)) {
            foreach ($array as $v) {
                if ($v['parent'] == $parent) {
                    $v['number'] = $number;
                    $arr[$v['id']] = $v;
                    for($i = 0; $i < $number; $i++){
                        $f .= "──";
                    }
                    $arr[$v['id']]['name'] = $f.$arr[$v['id']]['name'];
                    $arr[$v['id']]['number'] = $number;
                    $f = "├──";
                    $this->tree($array, $v['id'], $number + 1);
                }
            }
//            print_r($arr);
            return $arr;
        }
    }


}