<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 10:20
 * 权限管理类
 * 超级管理员 直接跳过验证
 * 普通管理 必须验证权限
 */

namespace Admin\Controller;


class RbacController extends BaseController
{

    public static function getLoginAccess()
    {

        $Model = D('Model')->select();//所有的操作模型

        foreach ($Model as $key => $value) {

            switch (intval($_SESSION['admin']['type'])) {
                case 1:
                    $_SESSION['admin']['Access'][$value['id']] = $value;
                    break;
                case 3:
                    $userid = explode(',', $value['user_id']);
                    if (in_array($_SESSION['admin']['id'], $userid) || $value['verify'] == 2) {
                        $_SESSION['admin']['Access'][$value['id']] = $value;
                    }
//                    if($value['controller'] != 'Setting'){
//                        $_SESSION['admin']['Access'][$value['id']] = $value;
//                    }
                    break;
                case 2:
                    $userid = explode(',', $value['user_id']);
                    if (in_array($_SESSION['admin']['id'], $userid) || $value['verify'] == 2) {
                        $_SESSION['admin']['Access'][$value['id']] = $value;
                    }
                    break;
                case 4:
                    $userid = explode(',', $value['user_id']);
                    if (in_array($_SESSION['admin']['id'], $userid) || $value['verify'] == 2) {
                        $_SESSION['admin']['Access'][$value['id']] = $value;
                    }
                    break;
                case 5:
                    $userid = explode(',', $value['user_id']);
                    if (in_array($_SESSION['admin']['id'], $userid) || $value['verify'] == 2) {
                        $_SESSION['admin']['Access'][$value['id']] = $value;
                    }
                    break;
            }


        }


    }

    public static function checkAccess($controller, $action)
    {

        if ($_SESSION['admin']['type'] == 1) {
            return true;
        } else {
            $data['controller'] = $controller;
            $data['action'] = $action;
            $model = D('Model')->where($data)->find();
            if (is_array($model) && !empty($model)) {
                if ($model['verify'] == 2) {
                    return true;
                }
                return array_key_exists($model['id'], $_SESSION['admin']['Access']);
            } else {
                return true;//数据库不存在直接不验证
            }

        }
        return false;

    }

    public static function getAction($controller, $action)
    {
        $reList = array();
        $data['controller'] = $controller;
        $data['action'] = $action;
        $model = D('Model')->where($data)->find();
        if ($model !== false) {
            $list = D('Model')->where(array('belongid' => $model['id'], 'level' => 3))->select();
            if ($list !== false) {

                foreach ($list as $key => $value) {

                    if ($_SESSION['admin']['type'] == 1 || $value['verify'] == 2) {
                        $reList[] = $value;
                    }
//                    else if($_SESSION['admin']['type'] == 3 || $value['verify'] == 2){
//                        if($value['controller'] != 'Setting'){
//                            $reList[] = $value;
//                        }
//                    }
                    else {
                        $arr = explode(',', $value['user_id']);
                        in_array($_SESSION['admin']['id'], $arr) && $reList[] = $value;
                    }


                }
                return $reList;
            }
        }
        return false;
    }

}