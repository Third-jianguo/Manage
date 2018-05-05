<?php
/**
 * Created by 坚果.
 * User: zq
 * 模板型控制器
 * Date: 2017/8/21 0021
 * Time: 12:49
 */

namespace Admin\Controller;


class SlideController extends TemplateController
{

    public function __construct()
    {
        parent::__construct();
        $GLOBALS['Template']['CONTROLLER_NAME'] = '轮播图';
        $GLOBALS['Template']['DBName'] = 'Slide';
    }
}