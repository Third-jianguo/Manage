<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 14:50
 */

namespace Admin\Controller;


class SettingController extends BaseController
{
    public function index()
    {
        $list = D('error')->order('error_id asc')->select();
        $list = $this->tree($list);
        $this->assign('list', $list);
        $this->display();
    }

    public function meun()
    {
        $list = D('Model')->where('level!=0')->select();
        $list = $this->tree($list);
        $this->assign('list', $list);
      //  print_r($list);
        $this->display();
    }

    public function add()
    {
        if (IS_POST) {

            $res = D('Model')->add($_POST);
            if ($res !== false) {
                ReturnJson(1, 10007);
            } else {
                ReturnJson(0, 10008);
            }

        } else {
            $list = D('Model')->where('level!=0')->select();
            $list = $this->tree($list);
            $this->assign('list', $list);
            $this->display();
        }

    }

    public function save()
    {
        if (IS_POST) {

            $res = D('Model')->save($_POST);
            if ($res !== false) {
                $this->set_admin_log('菜单被修改：'.$_POST['name']);

                ReturnJson(1, 10009);
            } else {
                ReturnJson(0, 10010);
            }

        } else {
            $info = D('Model')->find(intval($_GET['id']));
            $list = D('Model')->where('level!=0')->select();
            $list = $this->tree($list);
            $this->assign('list', $list);
            $this->assign('info', $info);
            $this->display();
        }

    }

    public function del()
    {
        $id = I('post.id', '0', 'intval');
        $m = M("Model")->find($id);
        if (D('Model')->delete($id) === false) {
            ReturnJson(0, 10012);
        } else {
            $this->set_admin_log('菜单被删除：'.$m['name'].$m['controller'].$m['action']);

            ReturnJson(1, 10011);
        }

    }
    public function error_add()
    {
        if (IS_POST) {

            $res = D('error')->add($_POST);
            if ($res !== false) {
                ReturnJson(1, 10007);
            } else {
                ReturnJson(0, 10008);
            }

        } else {
            $list = D('Error')->select();
            $this->assign('list', $list);
            $this->display();
        }

    }

    public function error_save()
    {
        if (IS_POST) {

            $res = D('error')->save($_POST);
            if ($res !== false) {
                ReturnJson(1, 10009);
            } else {
                ReturnJson(0, 10010);
            }

        } else {
            $info = D('Error')->find(intval($_GET['id']));
            $list = D('Error')->select();
            $this->assign('list', $list);
            $this->assign('info', $info);
            $this->display();
        }

    }

    public function error_del()
    {
        $id = I('post.id', '0', 'intval');
        if (D('error')->delete($id) === false) {
            ReturnJson(0, 10012);
        } else {
            ReturnJson(1, 10011);
        }

    }

//    public function tree($array, $parent = 0, $nubmer = 0)
//    {
//        static $arr = array();
//        if (is_array($array)) {
//            foreach ($array as $v) {
//                if ($v['belongid'] == $parent) {
//                    $v['number'] = $nubmer;
//                    $arr[$v['id']] = $v;
//                    $this->tree($array, $v['id'], $nubmer + 1);
//                }
//
//            }
//            return $arr;
//        }
//    }

}