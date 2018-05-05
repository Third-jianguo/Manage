<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 10:16
 */
/*
 * 基本增删改查访问控制器类
 */

namespace Admin\Controller;

use Think\Controller;

class TemplateController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        global $Template;
        $Template = array(
            'DBName' => 'Dbname',
            'CONTROLLER_NAME' => '模板控制',
            'WHERE' => array(),
            'ORDER' => '',
            'LOG' => 'title',
            'JOIN' => array(),              //join data
            'SHOW_FIELD' => '*',       //fieldName,123
            'CHECK_FIELD' => array(),       //fieldName => field
            'DB_DATA' => $_REQUEST,         //add、save data
        );
    }

    public function index($t = 'index')
    {
        global $Template;

        $model = D($Template['DBName'])->field($Template['SHOW_FIELD']);
        if(!empty($Template['JOIN'])){
            foreach($Template['JOIN'] as $val){
                $model->join($val, 'left');
            }
        }

        $model->where($Template['WHERE']);
        // page
        $countModel = M($Template['DBName']);
        if(!empty($Template['JOIN'])){
            foreach($Template['JOIN'] as $val){
                $countModel->join($val, 'left');
            }
        }
        $count = $countModel->where($Template['WHERE'])->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();

        if(!empty($Template['ORDER'])){
            $model->order($Template['ORDER']);
        }
        $model->limit($Page->firstRow.','.$Page->listRows);

        $list = $model->select();
        $this->assign('list', $list);
        $this->assign('page', $show);

        $this->assign('globals', $Template);
        $this->display($t);
    }

    public function add($t = 'add')
    {
        global $Template;

        if (IS_POST) {
            foreach ($Template['CHECK_FIELD'] as $key => $val) {
                if (empty($_POST[$val])) ReturnJson(0, $key . '不能为空');
            }
            $Template['DB_DATA']['createtime'] = time();

            $res = D($Template['DBName'])->add($Template['DB_DATA']);
            if ($res !== false) {
                if(!empty($_POST[$Template['LOG']])){
                    $this->set_admin_log($Template['CONTROLLER_NAME'].'：添加'.$_POST[$Template['LOG']]);
                }else{
                    $this->set_admin_log($Template['CONTROLLER_NAME'].'：添加'.$Template['LOG']);
                }

                ReturnJson(1, 10007);
            } else {
                ReturnJson(0, 10008);
            }
        }

        $this->display($t);
    }

    public function save($t = 'save')
    {
        global $Template;

        if (IS_POST) {
            foreach ($Template['CHECK_FIELD'] as $key => $val) {
                if (empty($_POST[$val])) ReturnJson(0, $key . '不能为空');
            }
            $res = D($Template['DBName'])->save($Template['DB_DATA']);
            if ($res !== false) {
                $this->set_admin_log($Template['CONTROLLER_NAME'] . '—修改：' . $Template['LOG']);
                ReturnJson(1, 10009);
            } else {
                ReturnJson(0, 10010);
            }
        }

        $info = M($Template['DBName'])->find($_GET['id']);
        $this->assign('info', $info);
        $this->display($t);
    }

    public function del()
    {
        global $Template;
        $id = I('post.id', '0', 'intval');
        $del = M($Template['DBName'])->find($id);
        $res1 = D($Template['DBName'])->delete($id);
        if ($res1 === false) {
            ReturnJson(0, 10012);
        } else {
            $log = empty($del[$Template['LOG']])?$_POST['title']:$del[$Template['LOG']];
            $this->set_admin_log($Template['CONTROLLER_NAME'] . '—删除：' . $log);
            ReturnJson(1, 10011);
        }
    }

    public function disable_data($field = 'status', $status = '1')
    {
        global $Template;
        $id = I('post.id', '0', 'intval');
        $disable = M($Template['DBName'])->find($id);
        if($disable[$field] == 1){
            $status = 0;
        }
        $res1 = D($Template['DBName'])->save(array('id' => $id, $field => $status));
        if ($res1 === false) {
            ReturnJson(0, 'no');
        } else {
            $this->set_admin_log($Template['CONTROLLER_NAME'] . '—修改状态：id:' . $id .'状态改为'. $status == 0?'正常':'关闭');

            ReturnJson(1, 'ok');
        }

    }
}