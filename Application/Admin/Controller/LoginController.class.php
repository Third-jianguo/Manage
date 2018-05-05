<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 10:15
 */
class LoginController extends Controller
{

    /**
     * 登录界面
     */
    public function index()
    {
        session('admin',null);
        $this->display();
    }

    public function Imgverify()
    {
        $config = array(

            'fontSize' => 20,              // 验证码字体大小(px)
            'useCurve' => false,            // 是否画混淆曲线
            'useNoise' => true,            // 是否添加杂点
            'imageH' => 40,               // 验证码图片高度
            'imageW' => 140,               // 验证码图片宽度
            'length' => 4,               // 验证码位数
        );
        $verify = new \Think\Verify($config);
        $verify->entry();

    }

    /**
     * 登录处理
     *
     */
    public function login()
    {

        if (IS_POST) {
            $code = I('post.code');
            $login['username'] = I('post.username');
            $login['password'] = I('post.password');
//            Validate::isNames($login['username'], 5, 20) || ReturnJson(0, 10000);
            Validate::isPWD($login['password'], 4, 20) ? $login['password'] = md5($login['password']) : ReturnJson(0, 10000);
            // $verify = new \Think\Verify();
            // !$verify->check($code) && ReturnJson(0, 10001);
            $response = D('Admin')->where($login)->find();
            if (is_array($response) && !empty($response)) {
                session('admin', $response);
                RbacController::getLoginAccess();
                $data['login_time'] = time();
                $data['login_ip'] = get_client_ip();
                $data['id'] = $_SESSION['admin']['id'];

                //添加管理员登录日志
                M("AdminLog")->add(array('admin_id' => $response['id'], 'admin_name' =>
                    $response['username'], 'time' => time(), 'login_ip' => get_client_ip(), 'details' => '登录' ,
                    'type' => 0));
                D('Admin')->save($data);
                ReturnJson(1, 10002);
            } else {
                ReturnJson(0, 10000);
            }
        }
    }
}