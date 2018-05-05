<?php
namespace Home\Model;

use Think\Model\RelationModel;


class OrderModel extends RelationModel
{
    public function check_create(){

        $money = $_POST['want_money'];
        $number = $_POST['want_number'];
        $advertising_id = $_POST['adv_id'];
        if(empty($money) || empty($number) || empty($advertising_id)){
            return_ajax("no");
        }
        $order_sn = date("di") . rand(1000, 9999);
        $data = array(
            'user_id' => $_SESSION['user']['id'],
            'adv_id' => $advertising_id,
            'order_sn' => $order_sn,
            'money' => $money,
            'number' => $number,
            'createtime' => time(),
        );
        M("Advertising")->save(array('id' => $advertising_id, 'status' => 1));

        return M("Order")->add($data);
    }
}