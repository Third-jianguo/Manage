<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 10:16
 */
/*
 * 后台访问控制器类
 * 验证是否登录及权限验证类
 */

namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!session('admin')) {
            IS_AJAX && ReturnJson(0, 10004);
            header("Location:".U("Login/index"));exit;
//            $this->error(errorCode(10004), U('Login/index'));
        }

        if (!RbacController::checkAccess(CONTROLLER_NAME, ACTION_NAME)) {
            IS_AJAX && ReturnJson(0, 10003);
            $this->error(errorCode(10003));
        }
        $ActBtns = RbacController::getAction(CONTROLLER_NAME, ACTION_NAME);
        if ($ActBtns !== false) {
            $this->assign('ActBtns', $ActBtns);
        }

    }

    public function clearchache()
    {
        unset($_SESSION['admin']['Access']);
        RbacController::getLoginAccess();
        ReturnJson(1, 10005);

    }

    public function upload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        // 上传单个文件
        $info = $upload->uploadOne($_FILES['image']);
        if (!$info) {// 上传错误提示错误信息
            ReturnJson(0, 10045);
            // $this->error($upload->getError());
        } else {// 上传成功 获取上传文件信息
            //var_dump($info);
            ReturnJson(1, '/Uploads/' . $info['savepath'] . $info['savename']);

        }
    }
    /*
     * add by 坚果
     * 上传单图返回图片链接
     */
    public function getImgUrl($name)
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/images'; // 设置附件上传根目录
        // 上传单个文件
        $info = $upload->uploadOne($_FILES[$name]);
        if (!$info) {// 上传错误提示错误信息
            ReturnJson(0, 10045);
            // $this->error($upload->getError());
        } else {
        // 上传成功 获取上传文件信息
            //var_dump($info);
            return '/Uploads/images/' . $info['savepath'] . $info['savename'];
        }
    }

    public function set_admin_log($str=''){
        $logdata = array(
            'admin_id'=>$_SESSION['admin']['id'],
            'admin_name'=>$_SESSION['admin']['username'],
            'time'=>time(),
            'login_ip'=>get_client_ip() ,
            'details'=>$str
        );
        D('admin_log')->add($logdata);
    }

    //返回用','分隔的id
    public function return_ids($arr,$str='id'){
        if(empty($arr)){
            return -1;
        }else{
            $list = array();
            foreach($arr as $k=>$v){
                $list[] = $v[$str];
            }
            return implode(',',$list);
        }
    }

    //返回用','分隔的字符串
    public function return_strs($arr,$str='sku'){
        if(empty($arr)){
            return -1;
        }else{
            $list = array();
            foreach($arr as $k=>$v){
                $list[] = "'".$v[$str]."'";
            }
            return implode(',',$list);
        }
    }

    public function return_all_name($arr,$str='name',$key='id'){
        if(empty($arr)){
            return array();
        }else{
            $list = array();
            foreach($arr as $k=>$v){
                $list[$v[$key]] = $v[$str];
            }
            return $list;
        }
    }

    //按权限返回小区列表
    public function get_regions(){
        if($_SESSION['admin']['type'] == 1 || $_SESSION['admin']['type'] == 3){
            return D('xcommunity_region')->order("display_order ASC")->select();
        }else{
            if(empty($_SESSION['admin']['region'])){
                return array();
            }else{
                return D('xcommunity_region')->where(" id IN (".$_SESSION['admin']['region'].")")->order("display_order ASC")->select();
            }
        }
    }

    //按小区权限返回where[_string]条件
    public function get_where_string($str = 'regionid'){
        if($_SESSION['admin']['type'] == 1 || $_SESSION['admin']['type'] == 3){
            return 1;
        }else{
            if(empty($_SESSION['admin']['region'])){
                return 0;
            }else{
                $idlist = explode(',',$_SESSION['admin']['region']);
                $where = ' 0 ';
                foreach($idlist as $k=>$v){
                    $where .= " OR find_in_set(".$v." , ".$str.") ";
                }
                return '('.$where.')';
            }
        }
    }

    //$arr1是否为$arr2的子集：1是0不是
    public function return_subset($arr1,$arr2,$admin=1){
        if($admin == 1){
            if($_SESSION['admin']['type'] == 1){
                return 1;
            }else{
                if(empty($arr1)){
                    return 1;
                }else if(empty($arr2)){
                    return 0;
                }else{
                    if($arr1 == array_intersect($arr1, $arr2)) {
                        return 1;
                    }else{
                        return 0;
                    }
                }
            }
        }else{
            if(empty($arr1)){
                return 1;
            }else if(empty($arr2)){
                return 0;
            }else{
                if($arr1 == array_intersect($arr1, $arr2)) {
                    return 1;
                }else{
                    return 0;
                }
            }
        }
    }

    //根据省id返回市列表json数据
    public function changesheng(){
        if(empty($_POST['id'])){
            $data = array();
        }else{
            $data = M('region','fc_')->where(" parent_id = ".$_POST['id'])->select();
        }
        echo json_encode($data);
    }

    //返回id的数组
    public function return_id_array($arr,$str='id'){
        if(empty($arr)){
            return -1;
        }else{
            $list = array();
            foreach($arr as $k=>$v){
                $list[] = $v[$str];
            }
            return $list;
        }
    }

    //返回树形结构数组
    public function tree($array, $str='belongid', $parent = 0, $nubmer = 0){
        static $arr = array();
        if (is_array($array)) {
            foreach ($array as $v) {
                if ($v[$str] == $parent) {
                    $v['number'] = $nubmer;
                    $v['number_str'] = $this->tree_str(0, $nubmer);
                    $arr[$v['id']] = $v;
                    $this->tree($array, $str, $v['id'], $nubmer + 1);
                }
            }
            return $arr;
        }
    }

    //树形结构前的‘├──’符号
    public function tree_str($parent = 0 ,$nubmer = 0){
        if($nubmer == $parent){
            return '';
        }else if($nubmer == $parent+1){
            return '├ ─ ─ ─ ─';
        }else if($nubmer > $parent+1){
            $str = '├ ─ ─ ─ ─';
            for($i = 0; $i < $nubmer-$parent-1; $i++){
                $str .= ' ─ ─ ─ ─';
            }
            return $str;
        }else{
            return '';
        }
    }

    //导出表格
    public function getExcel($fileName,$headArr,$data){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        $max = $key+25;
        foreach($headArr as $v){
            if($key > $max){
                $colum = 'A'.chr($key-26);
                $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
                $key += 1;
            }else{
                $colum = chr($key);
                $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
                $key += 1;
            }
        }
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                if($span > ord("A")+25){
                    $j = 'A'.chr($span-26);
                    $objActSheet->setCellValue($j.$column, $value);
                    $span++;
                }else{
                    $j = chr($span);
                    $objActSheet->setCellValue($j.$column, $value);
                    $span++;
                }
            }
            $column++;
        }
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }

    //模板列表
    public function get_template_list(){
        $token = getToken( true );
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=$token";
        $response = file_get_contents($url);
        $result = @json_decode($response, true);
        if($result['errcode'] == 41001){
            $token = getToken( true );
            $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=$token";
            $response = file_get_contents($url);
            $result = @json_decode($response, true);
        }
//        if(empty($result)) {
//            return error(-1, "接口调用失败, 元数据: {$response['meta']}");
//        } elseif(!empty($result['errcode'])) {
//            return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}, 错误详情：{$this->error_code($result['errcode'])}");
//        }
        return $result;
    }

    //图文列表
    public function get_news_list(){
        $token = getToken();
        $data = array('type'=>'news','offset'=>0,'count'=>20);
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=$token";
        $response = cPost($url,urldecode(json_encode($data)));
        $result = @json_decode($response, true);
        return $result;
    }

    //文章搜索
    public function searchtitle(){
        if(empty($_POST['title']))ReturnJson(0,'搜索文章标题不能为空');
        $where = array('title'=>array('LIKE','%'.$_POST['title'].'%'));
        $articlelist = D('xcommunity_news')->field("id,title")->where($where)->order(' createtime DESC ')->select();
        ReturnJson(1,$articlelist);
    }

}