<?php
namespace Home\Model;

use Think\Model\RelationModel;


class WalletModel extends RelationModel
{
//    检测钱包页面url的type传参是否符合当前可交易货币类型
    public function check_url_type(){

        $checkUrl = M("Currency")->where(array('id' => $_GET['type']))->find();//是否存在
        if(empty($checkUrl) && !empty($_GET['type'])){
            // 非法访问,转到index
            header("Location:".U("Wallet/index"));
            exit;
        }
        if(!empty($checkUrl)){
            $type = $checkUrl['name'];
            $sn = $checkUrl['sn'];
        }else{
            //默认的
            $show = M("Currency")->where(array('show' => 1))->find();
            $id = $show['id'];
            $type = $show['name'];
            $sn = $show['sn'];
        }
        $id = empty($id)?$_GET['type']:$id;

        return array('name' => $type,'sn' => $sn, 'id' => $id);
    }
}