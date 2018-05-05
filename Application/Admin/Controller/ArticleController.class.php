<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class ArticleController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '文章管理';
        $GLOBALS['Template']['DBName'] = 'Article';
    }



    public function save(){
        global $Template;
        if(IS_POST){
            if(empty($Template['DB_DATA']['image'])){
                unset($Template['DB_DATA']['image']);
            }
        }
        parent::save();
    }

}