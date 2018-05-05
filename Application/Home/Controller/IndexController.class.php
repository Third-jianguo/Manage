<?php
namespace Home\Controller;
use Think\Controller;
use QL\QueryList;

class IndexController extends BaseController {
    public function index(){
//        var_dump(cookie("lang"));
//        $t = M("Currency")->where(array('show' => 1))->getField("id");
//        $list = M('Advertising a')->field("a.*, user.id AS uid, user.name,user.head_img, curr.name as currency_name")
//            ->join('btc_user user ON a.user_id = user.id', 'left')
//            ->join('btc_currency curr ON a.currency_type = curr.id', 'left')
//            ->where(array('a.status' => 0, 'advertising_type' => 1))->order("id DESC")->limit(2)->select();
//
//        foreach($list as $key => $val){
//            $list[$key]['trustInfo'] = get_user_trust($val['user_id']);
//        }
//        $list2 = M('Advertising a')->field("a.*, user.id AS uid, user.name,user.head_img, curr.name as currency_name")
//            ->join('btc_user user ON a.user_id = user.id', 'left')
//            ->join('btc_currency curr ON a.currency_type = curr.id', 'left')
//            ->where(array('a.status' => 0, 'advertising_type' => 2))->order("id DESC")->limit(2)->select();
//
//        foreach($list2 as $key => $val){
//            $list2[$key]['trustInfo'] = get_user_trust($val['user_id']);
//        }
//        $slide = M("Slide")->where(array("status"=>0))->select();
//
//        $this->assign('slide', $slide);
//        $this->assign("list", $list);
//        $this->assign("list2", $list2);



        $arr = array(
            130,131,132,133,134,135,136,137,138,139,
            144,147,
            150,151,152,153,155,156,157,158,159,
            176,177,178,
            180,181,182,183,184,185,186,187,188,189,
        );
        $money = array(100, 300, 500, 800);
        for($i = 0; $i < 100; $i++) {
//            $tmp[] =  $arr[array_rand($arr)].' '.mt_rand(1000,9999).' '.mt_rand(1000,9999);
            $tmp[] =  $arr[array_rand($arr)].' **** '.mt_rand(1000,9999);
        }
        $tel_tmp = array_unique($tmp);
        foreach($tel_tmp as $key => $val){
            $tel[] = array('tel' => $val);
        }

        foreach($tel as $key => $val){

//            $tel[$key]['money'] = $money[array_rand($money)]." 美元";
            $rand = rand(6, 100);
            $zero = array('00', '000');

            $tel[$key]['money'] = $rand.$zero[array_rand($zero)]." 美元";

			$now = strtotime(date("Y-m-d"));
            $tel[$key]['createtime'] = mt_rand($now, time());
        }
//        var_export(array_unique($tmp));//去重

        $news = M("article")->order("display_order ASC , id DESC")->limit(3)->select();

        $announce = M("Announcement")->order("id DESC")->select();

        $slide = M("slide")->select();
        $video = M("img")->find(1);

        $this->assign("tel", $tel);
        $this->assign("slide", $slide);
        $this->assign("video", $video);
        $this->assign("announce", $announce);
        $this->assign("news", $news);
        $this->display();
    }

    public function video(){

        $video = M("video")->where(array('status' => 1))->select();

        $this->assign("video", $video);
        $this->display();
    }

    public function news(){

        $news = M("article")->find($_GET['id']);

        $this->assign("news", $news);
        if(!empty($_REQUEST['id'])){
            $this->display("new");
            exit;
        }
        $this->display();
    }

    public function test()
    {
        header("Content-type:text/html;charset=utf-8");
        vendor("phpQuery.phpQuery","",".php");
        vendor("QueryList.QueryList","",".php");
//-------------
//        $data = cPost("http://boscore.com/plugin/ladder/ladder_default.php",array('is_init' => 0, 'ld_mode' => 1));
//        $res = json_decode($data);

//        $hj = QueryList::Query("http://bbs.tianya.cn/list-develop-1.shtml",array("li"=>array('.num','html'), 'tx' => array('.tx', 'html')) );
//        $data = $hj->getData(function($x){
//            return $x;
//        });
//        print_r($data);
//
//---------------
        $hj = QueryList::Query("http://bbs.tianya.cn/list-develop-1.shtml",array("li"=>array('.author','html')) );
        $data = $hj->getData(function($x){
            return $x;
        });
        print_r($data);



    }

}