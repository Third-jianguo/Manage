<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class WalletlogController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '钱包日志';
        $GLOBALS['Template']['DBName'] = 'WalletLog';

    }

    public function index(){

        $where = array();
        $log_type = array('6' => '线上转出','10' => '交易买入', '20' => '交易卖出');
        if(!empty($_GET['keyword'])){
            $where['u.name'] = array("like", "%{$_GET['keyword']}%");
        }
        if(!empty($_GET['type']) && $_GET['type'] != 999){
            $where['log.money_type'] = $_GET['type'];
        }
        $list = M("WalletLog log")->field("log.*, u.name")
            ->join("btc_user u ON log.user_id = u.id", 'left')
            ->where($where)
            ->select();
        foreach($list as $key => $val){
            $list[$key]['shiji'] = $val['number'] - $val['service_money'];
            $list[$key]['type'] = $log_type[$val['log_type']];
        }
//        echo M("WalletLog log")->getLastSql();
        $this->assign("currency", M("currency")->select());
        $this->assign("list", $list);
        $this->display();
    }



}
