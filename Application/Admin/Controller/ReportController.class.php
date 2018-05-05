<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class ReportController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '用户举报';
        $GLOBALS['Template']['DBName'] = 'advertising_report';
    }

    public function index(){
        global $Template;

        $Template['DBName'] = "advertising_report report";
        $Template['SHOW_FIELD'] = "report.*, u.id as uid, u.head_img, u.name";
        $Template['JOIN'] = array("btc_user u  ON report.user_id = u.id");
        parent::index();
    }

    public function check(){
        $id = $_REQUEST['id'];
        M("advertising_report")->save(array('id' => $id, "status" => 1));
        ReturnJson(1, "ok");
    }

}
