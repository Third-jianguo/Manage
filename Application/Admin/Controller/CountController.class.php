<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class CountController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '统计';
        $GLOBALS['Template']['DBName'] = '';

    }

    public function index(){
        $where = array('o.status' => 40);
        $start = strtotime($_REQUEST['start_time']);
        $end = strtotime($_REQUEST['end_time']) + 86400;
        $where['o.createtime'] = array('BETWEEN', array($start, $end));
        if (empty($start)) {
            $where['o.createtime'] = array('ELT', $end);
        }
        if (empty($end) || $end == 86400) {
            $where['o.createtime'] = array('EGT', $start);
        }
        if (empty($start) && (empty($end) || $end == 86400) ) {
            unset($where['o.createtime']);
        }

        if(!empty($_GET['currency'])){
            $where['adv.currency_type'] = $_GET['currency'];
        }
        if(!empty($_GET['money'])){
            $where['adv.pay_currency_type'] = $_GET['money'];
        }

        $order = M("Order o")
            ->join("btc_advertising adv on adv.id = o.adv_id", 'left')
            ->join("btc_currency curr ON curr.id = adv.currency_type")
            ->field("sum(o.number) as number , sum(o.money) money, curr.sn, adv.pay_currency_type")
            ->group("adv.pay_currency_type, adv.currency_type")
            ->order("curr.sn")
            ->where($where)
            ->select();
//        echo M("Order o")->getLastSql();
        $order_count = M("Order o")->where(array('o.status' => 40))->count();


        $currency = M("currency")->select();
        $money = M("static_money")->select();


        $this->assign("order", $order);
        $this->assign("currency", $currency);
        $this->assign("money", $money);
        $this->assign("order_count", $order_count);
        $this->assign("search", $_REQUEST);
        $this->display();
    }

}
