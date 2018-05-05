<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 11:48
 */

namespace Admin\Controller;

class IndexController extends RbacController
{


    public function index()
    {
        $menu = MenuformatTree($_SESSION['admin']['Access'], 0);

        $this->assign('menu', $menu);
        $this->display();
    }

    public function welcome()
    {

//        $order = M('Order')->where('status <> 99 and status <> 40 ')->order("id DESC")->limit(5)->select();
//        $adv = M('Advertising adv')->field("adv.*, curr.sn")->join("btc_currency curr ON curr.id=adv.currency_type",
//            "left")->where(array
//        ("advertising_type" => 1))->order
//        ("adv.id DESC")
//            ->limit(5)->select();
//        $adv2 = M('Advertising adv')->field("adv.*, curr.sn")->join("btc_currency curr ON curr.id=adv.currency_type", "left")->where(array("advertising_type" => 2))->order("adv.id DESC")->limit(5)->select();
//
//        $this->assign("order", $order);
//        $this->assign("adv", $adv);
//        $this->assign("adv2", $adv2);

        $this->display();
    }

}