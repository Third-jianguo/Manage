<?php
namespace Home\Controller;

use Think\Controller;
use QL\QueryList;

class ApiController extends Controller
{

    public function lang()
    {
        cookie("lang", $_GET['lang'], 0);
        var_dump(cookie("lang"));

    }


    public function create_ewm()
    {
        vendor("phpqrcode.phpqrcode", '', ".php");
        $level = 'L';
        $size = 4;
        ob_clean();
        \QRcode::png($_REQUEST['data'], false, $level, $size);
    }

    public function test()
    {
        header("Content-type:text/html;charset=utf-8");
        vendor("phpQuery.phpQuery","",".php");
        vendor("QueryList.QueryList","",".php");
//地址 条件 分块
//        $data = QueryList::Query('http://aria-100.com/game_result.php',array("li"=> array('.result_box_ul_ul > li','html')),'.result_box_ul>li')->getData(function($item){
//            $item['list'] = QueryList::Query($item['list'],array(
//                'item' => array('.item','text')
//            ))->data;
//            return $item;
//        });


//        $hj = QueryList::Query('http://aria-100.com/game_result.php',array("li"=>array('.result_box_ul_ul > li','html')) );
//        $data = $hj->getData(function($x){
//            return $x['li'];
//        });
//        print_r($data);


//-------------
//        $data = cPost("http://boscore.com/plugin/ladder/ladder_default.php",array('is_init' => 0, 'ld_mode' => 1));
//        $res = json_decode($data);

//        $hj = QueryList::Query($res->score,array("li"=>array('.num','html'), 'tx' => array('.tx', 'html')) );
//        $data = $hj->getData(function($x){
//            return $x;
//        });
//        print_r($data);
//
//---------------
//===========
//        $hj = QueryList::Query("http://ntry.com/scores/keno_ladder/live.php",array("result_list"=>array('.latest_result_area > .result_list .round','html'), 'xxx' => array('.latest_result_area > .result_list .result', 'html')) );
//        $data = $hj->getData(function($x){
//            return $x;
//        });
//        print_r($data);

//        print_r(json_decode($data));
//===========

//        $data = cPost("http://boscore.com/plugin/ladder/ladder_result.php",array('is_init' => 0, 'ld_mode' => 1));
        $data = cPost("http://boscore.com/plugin/ladder/ladder_default.php",array('is_init' => 0, 'ld_mode' => 1));
        $res = json_decode($data);
        print_r($res->score);


        $hj = QueryList::Query($res->score, array("tx" => array(".tx", "html"), "num" => array('.num','html') ) );
        $data = $hj->getData(function($x){
            return $x;
        });
        print_r($data);

//        echo 'ok';
    }

    public function addChatLog()
    {
        $res = M("ChatLog")->add(array(
            'order_id' => intval($_GET['order_id']),
            'user_id' => intval($_GET['user_id']),
            'head_img' => empty($_GET['head_img']) ? '' : $_GET['head_img'],
            'content' => $_GET['content'],
            'is_img' => empty($_GET['is_img']) ? 0 : 1,
            'is_admin' => empty($_GET['is_admin']) ? 0 : 1,
            'createtime' => time(),
        ));
        if ($res) {
            return_ajax("ok", 1);
        }
        return_ajax("no");
    }

    public function getOrderInfo()
    {
        $order_info = M("Order")->where(array('id' => $_REQUEST['id']))->find();
        echo json_encode($order_info);
    }

    //定时器判断 返回正进行的超时订单
    public function checkOrder()
    {
        $order_info = M("Order o")->field("o.*, adv.user_id AS to_user_id, adv.pay_time")
            ->join("btc_advertising adv ON o.adv_id = adv.id", "left")
            ->where("o.status = 40 AND o.status = 99 ")->select();
        foreach ($order_info as $key => $val) {
            if (time() < ($order_info['createtime'] + ($order_info['pay_time'] * 60))) {
                unset($order_info[$key]);
            }
        }
        foreach ($order_info as $key => $val) {
            M("advertising")->save(array('id' => $order_info['adv_id'], 'status' => 0));
        }

        echo json_encode($order_info);
    }

    public function closeOrder()
    {
        $close = M("order")->where("status <> 99 and status <> 40 and unix_timestamp(now()) > 1800 + createtime")->select();
        foreach ($close as $key => $val) {
            M("advertising")->save(array('id' => $close['adv_id'], 'status' => 0));
        }
//        $res = M()->query('UPDATE btc_order SET status= 99 WHERE unix_timestamp(now()) > 1800 + createtime and status != 99 and status != 40 ');
//        print_r($$res);
        return_ajax("ok", 1);
    }


    //管理员桌面操作关闭
    public function closeAdv()
    {
        M("advertising")->save(array('id' => $_REQUEST['adv_id'], 'status' => 99));
        return_ajax("ok", 1);
    }

    public function changeHl()
    {
        $currency = M("currency")->where(array('status' => 0))->select();
        foreach ($currency as $key => $val) {

            $url = "https://gate.io/json_svr/query/?u=11&c=&type=ask_bid_list_table&symbol=" . strtolower($val['sn']) . "_usdt";
            $info = json_decode(file_get_contents($url), true);
            $json_val = $info['global_markets_table'];

            foreach (json_decode($json_val) as $k => $v) {
                if (strtolower($v->curr_b) == "krw" && strtolower($v->curr_a) == strtolower($val['sn']) && strtolower($v->name_en) == strtolower("upbit")) {
//                    print_r($v);
                    $hg = $v->last;
                    $zg = $v->last_cny;
                    M("money_info")->where(array('currency_id' => $val['id']))->save(array('krw' => $hg, 'cny' => $zg));

                }
            }

        }
    }


    //上传图片
    public function chat_img_upload()
    {

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        // 上传单个文件
        $info = $upload->uploadOne($_FILES['chatImg']);
        $path = "/Uploads/" . $info['savepath'] . $info['savename'];
        echo $path;
    }


}