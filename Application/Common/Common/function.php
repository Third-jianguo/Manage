<?php
/**
 * @param int $status 1 success other error
 * @param $code 错误代码代表的值
 */
function subtext($text, $length)
{
    if (mb_strlen($text, 'utf8') > $length)
        return mb_substr($text, 0, $length, 'utf8') . '...';
    return $text;
}

function ReturnJson($status = 1, $code)
{
    header('Content-type: application/json'); //json
    $msg = M('error')->where(array('error_id' => $code))->find();
    !$msg && $msg['error_detail'] = $code;
    exit(json_encode(array("status" => $status, "msg" => $msg['error_detail'])));
}

//输出错误信息值
function errorCode($code)
{
    $msg = M('error')->where(array(
        'error_id' => $code
    ))->find();
    !$msg && $msg['error_detail'] == $code;
    return $msg['error_detail'];

}

//生成导航菜单
function MenuformatTree($array, $pid = 0, $bs = 'belongid')
{
    if (is_array($array)) {
        $arr = array();
        $tem = array();
        foreach ($array as $v) {
            if ($v['level'] != 0) {
                if ($v[$bs] == $pid) {
                    $tem = MenuformatTree($array, $v['id']);
                    //判断是否存在子数组
                    $tem && $v['items'] = $tem;
                    $arr[$v['id']] = $v;
                }
            }
        }

        return $arr;
    }
}

//获取用户列表yby_user 以userid=>username形式返回 add by zq
function GetUserName()
{
    $usre_list = D('users')->field('id,name')->select();
    $users = array();
    foreach ($usre_list as $key => $val) {
        $users[$val['id']] = $val['name'];
    }
    return $users;
}

/**
 * @param $list
 * @param $nowPage
 * @param $listNums
 * 数组分页
 * @return array;
 */

function pageList($list, $nowPage, $listNums = 10)
{
    $count = count($list);//总数
    $toPages = ceil($count / $listNums);//总页数
    $pageList = array_slice($list, ($nowPage - 1) * $listNums, 10);
    return array('count' => $count, 'total' => $toPages, 'list' => $pageList, 'nowPage' => $nowPage);


}

function getOrderStatus( $status ){
    $orderStatus = array(0 => '已拍下', 10 => '待付款', 20 => '待收货',  30 => '待评价', 40 => '已完成', 99 => '已关闭');
    return $orderStatus[$status];
}

/**
 * 相差天数
 * @param $a
 * @param $b
 * @return float
 */
function count_days($a, $b)
{
    $d1 = strtotime($a);
    $d2 = strtotime($b);
    return $Days = round(($d1 - $d2) / 3600 / 24);
}

///无线递归/
function formatTree($array, $pid = 0, $bs = 'parent_id')
{
    if (is_array($array)) {
        $arr = array();
        $tem = array();
        foreach ($array as $v) {
            if ($v[$bs] == $pid) {
                $tem = formatTree($array, $v['id']);
                //判断是否存在子数组
                $tem && $v['items'] = $tem;
                $arr[] = $v;
            }
        }

        return $arr;
    }
}

/*
 * 获取token
 *
 * type: 1=服务号、2=订阅号
 */
function getToken($flag = false, $type = 1)
{
    $chat_model = M("Wxconfig");
    $chatInfo = $chat_model->where(array('id' => $type))->find();

    $token = unserialize($chatInfo['access_token']);
    if ($token['expire'] < time() || $flag) {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $chatInfo['appid'] . "&secret=" . $chatInfo['secret'];
        $tokenReturn = cGet($url);
        $token = json_decode($tokenReturn, true);
//        print_r($token);

        $record = array();
        $record['token'] = $token['access_token'];
        $record['expire'] = time() + 7000;
        $data['access_token'] = serialize($record);
        $chat_model->where(array('id' => $type))->save($data);
    } else {
        $token['access_token'] = $token['token'];
    }

    return $token['access_token'];
}

function cGet($url)
{
    //初始化
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    //打印获得的数据
    return $output;
}

function cPost($url, $post)
{
    //初始化
//    $headers = array("Content-type: application/json;charset='utf-8'");
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
//    curl_setopt($ch, CURLOPT_HEADER, $headers);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    //打印获得的数据
    return $output;
}

function uploadImgs($path = '')
{
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize = 51200000;// 设置附件上传大小
    $upload->exts = array();// 设置附件上传类型
    $upload->rootPath = './Uploads/'; // 设置上传根目录
    $upload->savePath = $path; // 设置附件上传（子）目录
// 上传文件
    $info = $upload->upload();
    return $info;

}

function send_mail( $sendto_email,$number, $subject = "admin", $body = '', $extra_hdrs = '', $user_name = ''){
    require_once 'class.phpmailer.php';
    include_once('class.smtp.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();                  // send via SMTP
    $mail->Host = "smtp.163.com";   // SMTP servers
    $mail->PORT = 994;
    $mail->SMTPAuth = true;           // turn on SMTP authentication
    $mail->Username = "18630319672";     // SMTP username  注意：普通邮件认证不需要加 @域名
    $mail->Password = "zxq5097"; // SMTP password
    $mail->From = "18630319672@163.com";      // 发件人邮箱
    $mail->FromName =  "发件人";  // 发件人
    $mail->CharSet = "UTF-8";   // 这里指定字符集！
    $mail->Encoding = "base64";
    $mail->AddAddress($sendto_email,"jianguoer");  // 收件人邮箱和姓名
    $mail->IsHTML(true);  // send as HTML
    $mail->Subject = $subject;
    $mail->Body = "<html><head><meta http-equiv=\"Content-Language\" content=\"zh-cn\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head> <body> I love php。 <br/> your email code is ".$number."  </body>  </html>  ";
    $mail->AltBody ="text/html";
    if(!$mail->Send())
    {
        return false;
        echo "邮件发送有误 <p>";
        echo "邮件错误信息: " . $mail->ErrorInfo;
        exit;
    }
    else {
        return true;
        echo "$user_name 邮件发送成功!<br />";
    }


    // 参数说明(发送到, 邮件主题, 邮件内容, 附加信息, 用户名)
//    send_mail("jianguo03@qq.com", "欢迎使用phpmailer！", "NULL", "yourdomain.com", "admin");

}
function isLogin( $call_back = ""){
    if(empty($_SESSION['user']) || empty($_SESSION['user']['id'])){
        if($call_back == ""){
            header("Location:/index.php/Home/User/login");
            exit;
        }
        header("Location:/index.php/Home/User/login?callback=".$call_back);
        exit;
    }
    return true;
}
//检测验证码
function check_verify($code, $id = "")
{
    $Verify = new \Think\Verify();
    return $Verify->check($code, $id);
}

//ajax返回数据
function return_ajax($msg, $code = 0 , $data = array())
{
    $detail = M("error")->where(array('error_id' => $msg))->getField("error_detail");
    if(empty($detail)){
        $res = array('code' => $code, 'msg' => $msg);
        if(!empty($data)){
            foreach ($data as $key => $val) {
                $res[$key] = $val;
            }
        }
        echo json_encode($res);
        exit;
    }else{
        $res = array('code' => $code, 'msg' => $detail);
        if(!empty($data)){
            foreach ($data as $key => $val) {
                $res[$key] = $val;
            }
        }
        echo json_encode($res);
        exit;
    }
}
function csrf(){
    return md5(rand(1000,99999));
}

//获取可交易货币种类
function get_currency(){
    return M("Currency")->where(array('status' => 0))->select();
}


/*
 * 获取钱包信息
 */
function get_wallet_info( $type = "")
{
    $where = array(
        'user_id' => $_SESSION['user']['id'],
    );
    if($type){
        $where['wallet_type'] = $type;
        $wallet = M("Wallet")->where($where)->find();
    }else{
        $wallet = M("Wallet")->where($where)->select();
    }

    return $wallet;
}

//获取用户的交易记录 好评度  信任次数
function get_user_trust( $uid = ''){
    if($uid == ''){
        $uid = $_SESSION['user']['id'];
    }
    $count = M("Order")->where(array('user_id' => $uid))->count();
    $orderCount = M("Advertising a")->join("btc_order o ON o.adv_id = a.id", "left")->where(array('a.user_id' =>
        $uid, 'o.user_id' => $uid, '_logic' => 'or'))->count();
    $goodCount = M("OrderComment")->where(array('user_id' => $uid))->count();

    if($orderCount){
        $good = round($goodCount / $orderCount , 2) * 100;
    }else{
        $good = 100;
    }
    $trust = M("user_trust")->where(array('to_user'=>$uid))->count();
    return array('dealCount' => $count, 'good' => $good."%", 'trust' => $trust); //交易次数 好评度 信任人数
}
function get_user_info( $uid ){
    return M("User")->field("head_img, name,id as uid")->where(array('id' => $uid))->find();
}

//function sendMail($to, $title, $content) {
//
//    Vendor('PHPMailer.PHPMailerAutoload');
//    $mail = new PHPMailer(); //实例化
//    $mail->IsSMTP(); // 启用SMTP
//    $mail->Host='smtp.exmail.qq.com'; //smtp服务器的名称（这里以QQ邮箱为例）
//    $mail->SMTPAuth = TRUE; //启用smtp认证
//    $mail->Username = '892259494@qq.com'; //你的邮箱名
//    $mail->Password = 'zqzxm123456' ; //邮箱密码
//    $mail->From = '892259494@qq.com'; //发件人地址（也就是你的邮箱地址）
//    $mail->FromName = '坚果'; //发件人姓名
//    $mail->AddAddress($to,"尊敬的客户");
//    $mail->WordWrap = 50; //设置每行字符长度
//    $mail->IsHTML(true); // 是否HTML格式邮件
//    $mail->CharSet= 'utf-8'; //设置邮件编码
//    $mail->Subject =$title; //邮件主题
//    $mail->Body = $content; //邮件内容
//    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
//
//
//    return($mail->Send());
//}
function check_mobile_code($tel, $code){

    return 1;
}

function check_email_code($email,$code){
    $check_code = M("Code")->where(array('tel' => $email))->find();
    if($code == $check_code['code'] && $check_code['createtime'] > time() - 600){
        return true;
    }
    return false;
}

function get_google_code(){
    vendor("google.GoogleAuthenticator","", ".php");
    $ga = new \PHPGangsta_GoogleAuthenticator();

    //创建一个新的"安全密匙SecretKey"
    //把本次的"安全密匙SecretKey" 入库,和账户关系绑定,客户端也是绑定这同一个"安全密匙SecretKey"
    return $ga->createSecret();
}



function googleCheck($secret,$google_code){
    vendor("google.GoogleAuthenticator","", ".php");
    $ga = new \PHPGangsta_GoogleAuthenticator();

    $oneCode = $ga->getCode($secret); //服务端计算"一次性验证码"
//    echo "服务端计算的验证码是:".$oneCode."<br/>";
    if($oneCode == $google_code){
        return true;
    }else{
        return false;
    }

//把提交的验证码和服务端上生成的验证码做对比
// $secret 服务端的 "安全密匙SecretKey"
// $oneCode 手机上看到的 "一次性验证码"
// 最后一个参数 为容差时间,这里是2 那么就是 2* 30 sec 一分钟.
// 这里改成自己的业务逻辑
//    $checkResult = $ga->verifyCode($google_code, $secret, 2);
//    $checkResult = $ga->verifyCode($secret, $oneCode, 2);
//    if ($checkResult) {
//        return true;
//        echo '匹配! OK'."<br/>";
//    } else {
//        return false;
//        echo '不匹配! FAILED'."<br/>";;
//    }


}

function languages($lang = 'zh-cn')
{
    $lang = empty($lang)?"zh-cn":$lang;
    $language = array(
        'zh-cn' => array(
            //首页
            'hello' => '你好',
            'btc' => '比特币',
            'index' => '首页',
            'buy' => '购买',
            'sell' => '出售',
            'sendAdvertising' => '发布广告',
            'sendAdvertisinged' => '发布的广告',
            'order' => '订单',
            'wallet' => '钱包',
            'about' => '关于我们',
            'rate' => '费率说明',
            'question' => '常见问题',
            'language_change' => '语言切换',
            'cn' => '简体中文',
            'hg' => '韩语',
            'login' => '登录',
            'register' => '注册',
            'open' => '打开',
            'close' => '关闭',
            'show' => '展开',
            'hide' => '收起',
            'edit' => '修改',
            'finish' => '完成',
            'please_login' => '请登录',

            'ok' => '确定',
            //出售/购买
            'transaction' => '交易',
            'search' => '搜索',
            'search_gg' => '搜广告',
            'search_user' => '搜用户',
            'no_adv' => '暂无广告',
            'please_select' => '请选择',
            'please_input' => '请输入',
            'can_not_transaction' => '暂时不支持该币种交易',
            'name' => '姓名',
            'nickname' => '昵称',
            'credit' => '信用',
            'paymentMethod' => '付款方式',
            'quota' => '限额',
            'price' => '价格',
            'goodReputation' => '好评度',
            'trust' => '信任',
            'trust_ok' => '已信任',
            'pingbi' => '屏蔽',
            'pingbi_ok' => '已屏蔽',
            'complain' => '举报这则广告',
            'complainContent' => '举报是指您对此交易有不满或者给我们反馈其他情况，仅用于反馈情况，我们核实后会进行相关处理，如果订单遇到纠纷，请通过申诉渠道解决。',
            'noRespond' => '一直不响应',
            'noPay' => '恶意下单不付款',
            'deceive' => '伪造证据欺诈',
            'induce' => '诱导其他交易',
            'spite' => '态度差',
            'qizha' => '涉嫌欺诈',
            'bufangbi' => '已付款不放币',
            'falseAdvertising' => '挂虚假广告',
            'feedback' => '请在此反馈更多内容',
            'anonymitySubmit' => '匿名举报',
            //buy
            'dealCount' => '交易次数',
            'trustCount' => '信任人数',
            'dealHistory' => '历史成交',
            'priceReport' => '报价',
            'dealLimit' => '交易限额',
            'paymentTime' => '付款期限',
            'buyHowMany' => '你想购买多少',
            'buyHowMuch' => '输入您想购买的金额',
            'buyHowManyNumber' => '输入你想购买的数量',
            'realNameAuthentication' => '进行实名认证',
            'realName' => '实名认证',
            'realNameAuthenticationMust' => '该广告主需要您进行实名认证之后才能进行交易',
            'only_trust' => '对方只与信任的人进行交易',
            'dealKnow' => '交易须知',
            'dealKnowContent' => '1.交易前请详细了解对方的交易信息。
                        2.请通过平台进行沟通约定，并保存好相关聊天记录。
                        3.如遇到交易纠纷，可通过申诉来解决问题。
                        4.在您发起交易请求后，BTC 被锁定在托管中，受到 CoinCola 保护。如果您是买家，发起交易请求后，请在付款周期内付款并把交易标记为付款已完成。卖家在收到付款后将会放行处于托管中的 BTC。
                        交易前请阅读《CoinCola服务条款》以及常见问题、交易指南等帮助文档。
                        5.请注意欺诈风险，交易前请检查该用户收到的评价和相关信用信息，并对新近创建的账户多加留意。
                        6.托管服务保护网上交易的买卖双方。在发生争议的情况下，我们将评估所提供的所有信息，并将托管的 BTC 放行给其合法所有者。',
            'buyNow' => '立即购买',
            //sell
            'sellHowMany' => '你想出售多少',
            'sellHowMuch' => '输入你想出售的金额',
            'sellHowManyNumber' => '输入你想出售的数量',
            'sellNow' => '立即出售',
            //发布
            'release' => '发布一个',
            'country' => '国家',
            'minute' => '分钟',
            'begin_time' => '开始时间',
            'end_time' => '结束时间',
            'dealAdvertising' => '交易广告',
            'dealType' => '交易类型',
            'advertisingType' => '广告类型',
            'selectAdvertisingType' => '选择广告类型',
            'sellOnline' => '在线出售',
            'buyOnline' => '在线购买',
            'buyAddress' => '所在地',
            'buyAddressD' => '请选择你要发布广告的国家',
            'more' => '更多信息',
            'formMoneyType' => '货币',
            'formMoneyTypeD' => '您希望交易付款的货币类型',
            'formPremium' => '溢价',
            'formPremiumD' => '基于市场价的溢出比例，市场价是根据部分大型交易所实时价格得出的，确保您的报价趋于一个相对合理范围，比如当前价格为7000，溢价比例为10%，那么价格为7700。',
            'formPrice' => '价格',
            'formPriceD' => '基于溢价比例得出的报价，10分钟更新一次',
            'formPriceD2' => '当前价格',
            'input_price' => '溢价比例',
            'priceLow' => '最低价（选填）',
            'priceLowD' => '广告最低可成交的价格，可帮助您在价格剧烈波动时保持稳定的盈利，比如最低价为12000，市场价处于12000以下时，您的广告将依旧以12000的价格展示出来',
            'priceHigh' => '最高价（选填）',
            'priceHighD' => '广告最高可成交的价格，可帮助您在价格剧烈波动时保持稳定的盈利，比如最高价为12000，市场价处于12000以上时，您的广告将依旧以12000的价格展示出来',
            'quotaLow' => '最小限额',
            'quotaLowD' => '一次交易的最低的交易限制。',
            'quotaHigh' => '最大限额',
            'quotaHighD' => '一次交易中的最大交易限制，您的钱包余额也会影响最大量的设置。',
            'proceeds' => '收款方式',
            'formPaymentTime' => '付款期限',
            'formPaymentTimeD' => '您承诺在此期限内付款（分钟）',
            'advertisingComment' => '广告留言',
            'formComment' => '请说明有关您交易的相关条款或备注您的支付方式,如微信号,支付宝等,以便对方可以快速和您交易（下单前后都可见）',
            'showAdvanced' => '显示高级设置',
            'hideAdvanced' => '隐藏高级设置',
            'advanced' => '高级设置',
            'isSafety' => '是否启用安全选项',
            'isSafetyD' => '启用后，仅限于已验证身份证和通过短信验证的用户与本广告交易。',
            'enable' => '启用',
            'is_enable' => '已启用',
            'disable' => '不启用',
            'onlytrust' => '仅限受信任的交易者：启用后，仅限于自己信任的用户与本广告交易。',
            'onlytrustD' => '启用后，仅限于自己信任的用户与本广告交易。',
            'openTime' => '开放时间',
            'openTimeD' => '您希望广告自动显示和隐藏的天数和小时数。',
            'submitNow' => '立即发布',
            //用户中心
            'please_input_name' => '请输入用户名',
            'forgetPassword' => '忘记密码',
            'login_pwd' => '登录密码',
            'edit_pwd' => '修改登录密码',
            'edit_money_pwd' => '修改资金密码',
            'money_pwd_info' => '资金密码将用于比特币的提款，保护您的比特币不被他人轻易盗用',
            'old_pwd' => '请输入原密码',
            'new_pwd' => '输入新密码',
            'new_pwd2' => '请再次输入新密码',
            'no_pipei' => '两次密码输入不一致',
            'pwd_tip' => '登录密码请不要与资金密码或其他网站密码一致，由此产生的账号被盗，本站概不负责。',
            'userHome' => '用户中心',
            'myAdvertising' => '我的广告',
            'sell_adv' => '出售广告',
            'buy_adv' => '购买广告',
            'out' => '退出',
            'userInfo' => '用户资料',
            'authentication' => '身份验证',
            'email' => '电子邮件',
            'mobile' => '手机号码',
            'registerTime' => '注册时间',
            'dealFirst' => '第一次交易时间',
            'language' => '语言',
            'trustNum' => '信任人数',
            'trusted' => '被',
            'trusted2' => '人信任',
            'paymentCount' => '累计交易次数',
            'paymentCounts' => '累计交易量',
            'individual' => '简介,在您的公共资料上展示您的介绍信息。纯文本,不超过200字',
            'individualComment' => '请勿填写您 CoinCola 的注册邮箱，避免收到钓鱼邮件',
            'lang_idcard' => '为确保交易安全，保障您的合法权益，请您完成身份验证！我们提供安全高效实时联网的官方身份认证服务，具有更高级别的安全保护。身份验证一旦成功，不予修改和解除认证。建议您谨慎选择，真实填写。',
            'go_to_rz' => '去认证',
            'idCard' => '身份证',
            'passport' => '护照',
            'passportD' => '适用于中国港澳台及其他外国用户',
            'driving' => '驾照',
            'verify' => '已验证',
            'unverified' => '未验证',
            'uploadInfo' => '请上传图片小于2M',
            'upload' => '上传图片',
            'uploadRule' => '上传图片注意事项：

                        您上传的驾照图片是真实有效的

                        您的驾照图片的信息清晰可见

                        您上传的图片没有反光或是折痕情况

                        您上传的图片为彩色图像',
            'trustYou' => '信任您的人',
            'youTrust' => '您信任的人',
            'untrust' => '您屏蔽的人',
            'noSomething' => '暂无相关信息',
            'save' => '保存',
            'user_info' => '基本信息',
            'anquan_moeny' => '账户安全',
            'anquan_setting' => '安全设置',
            'is_trusted' => '受信任的',
            'bind_tel' => '绑定手机',
            'bind_telD' => '提现，修改密码，及安全设置时用以收取验证短信',
            'pwdD' => '提现、修改安全设置时输入',
            'bind_google' => '绑定谷歌验证',
            'bind_googleD' => '绑定后，登录、提现时需要谷歌二次验证',
            'use_login' => '用于登录账户时输入',
            'bind_email' => '绑定邮箱',
            'bind_emailD' => '用于登录、提币、修改安全设置时输入',
            'verify_email' => '验证邮箱',
            'please_input_email' => '请输入邮箱地址',
            'please_input_code' => '请输入验证码',
            'send_code' => '发送验证码',
            'err_code' => '验证码错误',
            'next' => '下一步',
            'find_pwd' => '找回密码',
            'find_pwdD' => '请通过手机或邮箱来找回密码',
            'find_use_tel' => '手机找回',
            'find_use_email' => '邮箱找回',
            'change_pwd' => '密码重置',
            'change_pwdD' => '请按要求设置您的新密码',


            'google1' => '请先扫码二维码或手工输入密钥，将手机上生成的动态验证码填到下面的输入框',
            'account_name' => '账户名称',
            'key' => '密钥',
            'google_code' => '谷歌验证码',
            'how_to_app_google' => '如何安装谷歌验证码',
            'how_to_app_google1' => '在App Store中搜索Google Authenticator下载应用',
            'how_to_app_google2' => '在安卓应用市场中搜索“谷歌身份验证器”，或搜索Google Authenticator下载应用',
            'google_submit' => '验证并启用',
            'order_manage' => '订单管理',
            'the_ing' => '进行中',
            'the_end' => '已结束',
            'deal_people' => '交易伙伴',
            'order_sn' => '订单编号',
            'deal_type' => '类型',
            'deal_money' => '交易金额',
            'deal_number' => '交易数量',
            'createtime' => '创建时间',
            'deal_op' => '交易操作',
            'order_status' => '交易状态',
            'status' => '状态',
            'oked' => '已完成',
            'closed' => '已关闭',
            'deal_cancel' => '取消交易',
            'return_chat' => '返回聊天',


            //钱包
            'balance' => '钱包可用余额',
            'moneyAvailable' => '可用资产',
            'moneyAll' => '总资产',
            'moneyFreeze' => '冻结资产',
            'moneyType' => '币种',
            'recharge' => '充值',
            'withdraw' => '提现',
            'refresh ' => '刷新',
            'receive' => '接收',
            'send' => '发送',
            'sendD' => '发送数量，本次最多可发送',
            'receiveAddress' => '接收地址',
            'manageAddress' => '管理地址',
            'paymentLog' => '交易记录',
            'all' => '全部',
            'wallet_tip' => '温馨提示',
            'networkIn' => '网络转入',
            'networkOut' => '网络转出',
            'paymentIn' => '交易买入',
            'paymentOut' => '交易卖出',
            'terraceIn' => '平台转入',
            'terraceOut' => '平台转出',
            'serviceCharge' => '手续费',
            'awardMoney' => '提成奖金',
            'activityMoney' => '活动奖励',
            'noDetail' => '暂无账户明细',
            'sendAddress' => '发送地址',
            'number' => '数量',
            'passwordToMoney' => '资金密码',
            'remark' => '备注',
            'lang_what' => '您想要创建什么样的交易广告？如果您希望出售',
            'lang_what2' => '请确保您的钱包中有',
            'Monday' => "星期一",
            'Tuesday' => "星期二",
            'Wednesday' => "星期三",
            'Thursday' => "星期四",
            'Friday' => "星期五",
            'Saturday' => "星期六",
            'Sunday' => "星期日",
            //chat
            'yipaixia' => '已拍下',
            'please_input_more' => '请填写申诉内容',
            'order_well_save' => '订单将在托管中保存',
            'order_well_save2' => '分钟, 逾期未付款将自动取消',
            'order_info' => '订单信息',
            'chat' => '聊天',
            'sell_info' => '卖家信息',
            'buy_info' => '买家信息',
            'say_something' => '说点什么吧',
            'deal_tip' => '交易提示',
            'deal_tip1' => '1.对方的 BTC 已被托管锁定，您需要在规定时间内完成付款并点击“标记付款已完成”按钮，否则交易将自动取消。<br>2.转账时请在留言附上订单编号，否则对方可能无法识别您的付款。',
            'deal_tip2' => '您已标记付款完成，卖家在收到付款后将放行 BTC 给您。可主动联系对方，沟通确认款项',
            'pay_ok' => '标记已付款完成',
            'receive_ok' => '标记已收款完成',
            'toUser' => '对用户',
            'common' => '留下评论',
            'good' => '好评',
            'good1' => '好评',
            'good2' => '差评',
            'submit_common' => '提交评论',
            'deal_question' => '交易遇到问题',
            'shensu_tip' => '如果对方未响应，不确认或者对交易条款发生纠纷，您可以申诉此交易',
            'shensu' => '申诉',
            'shensu_why' => '申诉原因',
            'shensu_why1' => '我已付款，但卖家未放行',
            'shensu_why2' => '卖家未遵守交易广告条款',
            'say_your_question' => '请简单描述你的问题',
        ),
        'en' => array(
            'hello' => 'hello',
        ),
        'ko-kr' => array(
            //首页
            'hello' => '你好',
            'btc' => '比特币',
            'index' => '首页',
            'buy' => '购买',
            'sell' => '出售',
            'sendAdvertising' => '发布广告',
            'sendAdvertisinged' => '发布的广告',
            'order' => '订单',
            'wallet' => '钱包',
            'about' => '关于我们',
            'rate' => '费率说明',
            'question' => '常见问题',
            'language_change' => '语言切换',
            'cn' => '简体中文',
            'hg' => '韩语',
            'login' => '登录',
            'register' => '注册',
            'open' => '打开',
            'close' => '关闭',
            'show' => '展开',
            'hide' => '收起',
            'edit' => '修改',
            'finish' => '完成',
            'please_login' => '请登录',

            'ok' => '确定',
            //出售/购买
            'transaction' => '交易',
            'search' => '搜索',
            'search_gg' => '搜广告',
            'search_user' => '搜用户',
            'no_adv' => '暂无广告',
            'please_select' => '请选择',
            'please_input' => '请输入',
            'can_not_transaction' => '暂时不支持该币种交易',
            'name' => '姓名',
            'nickname' => '昵称',
            'credit' => '信用',
            'paymentMethod' => '付款方式',
            'quota' => '限额',
            'price' => '价格',
            'goodReputation' => '好评度',
            'trust' => '信任',
            'trust_ok' => '已信任',
            'pingbi' => '屏蔽',
            'pingbi_ok' => '已屏蔽',
            'complain' => '举报这则广告',
            'complainContent' => '举报是指您对此交易有不满或者给我们反馈其他情况，仅用于反馈情况，我们核实后会进行相关处理，如果订单遇到纠纷，请通过申诉渠道解决。',
            'noRespond' => '一直不响应',
            'noPay' => '恶意下单不付款',
            'deceive' => '伪造证据欺诈',
            'induce' => '诱导其他交易',
            'spite' => '态度差',
            'qizha' => '涉嫌欺诈',
            'bufangbi' => '已付款不放币',
            'falseAdvertising' => '挂虚假广告',
            'feedback' => '请在此反馈更多内容',
            'anonymitySubmit' => '匿名举报',
            //buy
            'dealCount' => '交易次数',
            'trustCount' => '信任人数',
            'dealHistory' => '历史成交',
            'priceReport' => '报价',
            'dealLimit' => '交易限额',
            'paymentTime' => '付款期限',
            'buyHowMany' => '你想购买多少',
            'buyHowMuch' => '输入您想购买的金额',
            'buyHowManyNumber' => '输入你想购买的数量',
            'realNameAuthentication' => '进行实名认证',
            'realName' => '实名认证',
            'realNameAuthenticationMust' => '该广告主需要您进行实名认证之后才能进行交易',
            'only_trust' => '对方只与信任的人进行交易',
            'dealKnow' => '交易须知',
            'dealKnowContent' => '1.交易前请详细了解对方的交易信息。
                        2.请通过平台进行沟通约定，并保存好相关聊天记录。
                        3.如遇到交易纠纷，可通过申诉来解决问题。
                        4.在您发起交易请求后，BTC 被锁定在托管中，受到 CoinCola 保护。如果您是买家，发起交易请求后，请在付款周期内付款并把交易标记为付款已完成。卖家在收到付款后将会放行处于托管中的 BTC。
                        交易前请阅读《CoinCola服务条款》以及常见问题、交易指南等帮助文档。
                        5.请注意欺诈风险，交易前请检查该用户收到的评价和相关信用信息，并对新近创建的账户多加留意。
                        6.托管服务保护网上交易的买卖双方。在发生争议的情况下，我们将评估所提供的所有信息，并将托管的 BTC 放行给其合法所有者。',
            'buyNow' => '立即购买',
            //sell
            'sellHowMany' => '你想出售多少',
            'sellHowMuch' => '输入你想出售的金额',
            'sellHowManyNumber' => '输入你想出售的数量',
            'sellNow' => '立即出售',
            //发布
            'release' => '发布一个',
            'country' => '国家',
            'minute' => '分钟',
            'begin_time' => '开始时间',
            'end_time' => '结束时间',
            'dealAdvertising' => '交易广告',
            'dealType' => '交易类型',
            'advertisingType' => '广告类型',
            'selectAdvertisingType' => '选择广告类型',
            'sellOnline' => '在线出售',
            'buyOnline' => '在线购买',
            'buyAddress' => '所在地',
            'buyAddressD' => '请选择你要发布广告的国家',
            'more' => '更多信息',
            'formMoneyType' => '货币',
            'formMoneyTypeD' => '您希望交易付款的货币类型',
            'formPremium' => '溢价',
            'formPremiumD' => '基于市场价的溢出比例，市场价是根据部分大型交易所实时价格得出的，确保您的报价趋于一个相对合理范围，比如当前价格为7000，溢价比例为10%，那么价格为7700。',
            'formPrice' => '价格',
            'formPriceD' => '基于溢价比例得出的报价，10分钟更新一次',
            'input_price' => '溢价比例',
            'priceLow' => '最低价（选填）',
            'priceLowD' => '广告最低可成交的价格，可帮助您在价格剧烈波动时保持稳定的盈利，比如最低价为12000，市场价处于12000以下时，您的广告将依旧以12000的价格展示出来',
            'priceHigh' => '最高价（选填）',
            'priceHighD' => '广告最高可成交的价格，可帮助您在价格剧烈波动时保持稳定的盈利，比如最高价为12000，市场价处于12000以上时，您的广告将依旧以12000的价格展示出来',
            'quotaLow' => '最小限额',
            'quotaLowD' => '一次交易的最低的交易限制。',
            'quotaHigh' => '最大限额',
            'quotaHighD' => '一次交易中的最大交易限制，您的钱包余额也会影响最大量的设置。',
            'proceeds' => '收款方式',
            'formPaymentTime' => '付款期限',
            'formPaymentTimeD' => '您承诺在此期限内付款（分钟）',
            'advertisingComment' => '广告留言',
            'formComment' => '请说明有关您交易的相关条款或备注您的支付方式,如微信号,支付宝等,以便对方可以快速和您交易（下单前后都可见）',
            'showAdvanced' => '显示高级设置',
            'hideAdvanced' => '隐藏高级设置',
            'advanced' => '高级设置',
            'isSafety' => '是否启用安全选项',
            'isSafetyD' => '启用后，仅限于已验证身份证和通过短信验证的用户与本广告交易。',
            'enable' => '启用',
            'is_enable' => '已启用',
            'disable' => '不启用',
            'onlytrust' => '仅限受信任的交易者：启用后，仅限于自己信任的用户与本广告交易。',
            'onlytrustD' => '启用后，仅限于自己信任的用户与本广告交易。',
            'openTime' => '开放时间',
            'openTimeD' => '您希望广告自动显示和隐藏的天数和小时数。',
            'submitNow' => '立即发布',
            //用户中心
            'please_input_name' => '请输入用户名',
            'forgetPassword' => '忘记密码',
            'login_pwd' => '登录密码',
            'edit_pwd' => '修改登录密码',
            'edit_money_pwd' => '修改资金密码',
            'money_pwd_info' => '资金密码将用于比特币的提款，保护您的比特币不被他人轻易盗用',
            'old_pwd' => '请输入原密码',
            'new_pwd' => '输入新密码',
            'new_pwd2' => '请再次输入新密码',
            'no_pipei' => '两次密码输入不一致',
            'pwd_tip' => '登录密码请不要与资金密码或其他网站密码一致，由此产生的账号被盗，本站概不负责。',
            'userHome' => '用户中心',
            'myAdvertising' => '我的广告',
            'sell_adv' => '出售广告',
            'buy_adv' => '购买广告',
            'out' => '退出',
            'userInfo' => '用户资料',
            'authentication' => '身份验证',
            'email' => '电子邮件',
            'mobile' => '手机号码',
            'registerTime' => '注册时间',
            'dealFirst' => '第一次交易时间',
            'language' => '语言',
            'trustNum' => '信任人数',
            'trusted' => '被',
            'trusted2' => '人信任',
            'paymentCount' => '累计交易次数',
            'paymentCounts' => '累计交易量',
            'individual' => '简介,在您的公共资料上展示您的介绍信息。纯文本,不超过200字',
            'individualComment' => '请勿填写您 CoinCola 的注册邮箱，避免收到钓鱼邮件',
            'lang_idcard' => '为确保交易安全，保障您的合法权益，请您完成身份验证！我们提供安全高效实时联网的官方身份认证服务，具有更高级别的安全保护。身份验证一旦成功，不予修改和解除认证。建议您谨慎选择，真实填写。',
            'go_to_rz' => '去认证',
            'idCard' => '身份证',
            'passport' => '护照',
            'passportD' => '适用于中国港澳台及其他外国用户',
            'driving' => '驾照',
            'verify' => '已验证',
            'unverified' => '未验证',
            'uploadInfo' => '请上传图片小于2M',
            'upload' => '上传图片',
            'uploadRule' => '上传图片注意事项：

                        您上传的驾照图片是真实有效的

                        您的驾照图片的信息清晰可见

                        您上传的图片没有反光或是折痕情况

                        您上传的图片为彩色图像',
            'trustYou' => '信任您的人',
            'youTrust' => '您信任的人',
            'untrust' => '您屏蔽的人',
            'noSomething' => '暂无相关信息',
            'save' => '保存',
            'user_info' => '基本信息',
            'anquan_moeny' => '账户安全',
            'anquan_setting' => '安全设置',
            'is_trusted' => '受信任的',
            'bind_tel' => '绑定手机',
            'bind_telD' => '提现，修改密码，及安全设置时用以收取验证短信',
            'pwdD' => '提现、修改安全设置时输入',
            'bind_google' => '绑定谷歌验证',
            'bind_googleD' => '绑定后，登录、提现时需要谷歌二次验证',
            'use_login' => '用于登录账户时输入',
            'bind_email' => '绑定邮箱',
            'bind_emailD' => '用于登录、提币、修改安全设置时输入',
            'verify_email' => '验证邮箱',
            'please_input_email' => '请输入邮箱地址',
            'please_input_code' => '请输入验证码',
            'send_code' => '发送验证码',
            'err_code' => '验证码错误',
            'next' => '下一步',
            'find_pwd' => '找回密码',
            'find_pwdD' => '请通过手机或邮箱来找回密码',
            'find_use_tel' => '手机找回',
            'find_use_email' => '邮箱找回',
            'change_pwd' => '密码重置',
            'change_pwdD' => '请按要求设置您的新密码',


            'google1' => '请先扫码二维码或手工输入密钥，将手机上生成的动态验证码填到下面的输入框',
            'account_name' => '账户名称',
            'key' => '密钥',
            'google_code' => '谷歌验证码',
            'how_to_app_google' => '如何安装谷歌验证码',
            'how_to_app_google1' => '在App Store中搜索Google Authenticator下载应用',
            'how_to_app_google2' => '在安卓应用市场中搜索“谷歌身份验证器”，或搜索Google Authenticator下载应用',
            'google_submit' => '验证并启用',
            'order_manage' => '订单管理',
            'the_ing' => '进行中',
            'the_end' => '已结束',
            'deal_people' => '交易伙伴',
            'order_sn' => '订单编号',
            'deal_type' => '类型',
            'deal_money' => '交易金额',
            'deal_number' => '交易数量',
            'createtime' => '创建时间',
            'deal_op' => '交易操作',
            'order_status' => '交易状态',
            'status' => '状态',
            'oked' => '已完成',
            'closed' => '已关闭',
            'deal_cancel' => '取消交易',
            'return_chat' => '返回聊天',


            //钱包
            'balance' => '钱包可用余额',
            'moneyAvailable' => '可用资产',
            'moneyAll' => '总资产',
            'moneyFreeze' => '冻结资产',
            'moneyType' => '币种',
            'recharge' => '充值',
            'withdraw' => '提现',
            'refresh ' => '刷新',
            'receive' => '接收',
            'send' => '发送',
            'sendD' => '发送数量，本次最多可发送',
            'receiveAddress' => '接收地址',
            'manageAddress' => '管理地址',
            'paymentLog' => '交易记录',
            'all' => '全部',
            'wallet_tip' => '温馨提示',
            'networkIn' => '网络转入',
            'networkOut' => '网络转出',
            'paymentIn' => '交易买入',
            'paymentOut' => '交易卖出',
            'terraceIn' => '平台转入',
            'terraceOut' => '平台转出',
            'serviceCharge' => '手续费',
            'awardMoney' => '提成奖金',
            'activityMoney' => '活动奖励',
            'noDetail' => '暂无账户明细',
            'sendAddress' => '发送地址',
            'number' => '数量',
            'passwordToMoney' => '资金密码',
            'remark' => '备注',
            'lang_what' => '您想要创建什么样的交易广告？如果您希望出售',
            'lang_what2' => '请确保您的钱包中有',
            'Monday' => "星期一",
            'Tuesday' => "星期二",
            'Wednesday' => "星期三",
            'Thursday' => "星期四",
            'Friday' => "星期五",
            'Saturday' => "星期六",
            'Sunday' => "星期日",
            //chat
            'yipaixia' => '已拍下',
            'please_input_more' => '请填写申诉内容',
            'order_well_save' => '订单将在托管中保存',
            'order_well_save2' => '分钟, 逾期未付款将自动取消',
            'order_info' => '订单信息',
            'chat' => '聊天',
            'sell_info' => '卖家信息',
            'buy_info' => '卖家信息',
            'say_something' => '说点什么吧',
            'deal_tip' => '交易提示',
            'deal_tip1' => '1.对方的 BTC 已被托管锁定，您需要在规定时间内完成付款并点击“标记付款已完成”按钮，否则交易将自动取消。<br>2.转账时请在留言附上订单编号，否则对方可能无法识别您的付款。',
            'deal_tip2' => '您已标记付款完成，卖家在收到付款后将放行 BTC 给您。可主动联系对方，沟通确认款项',
            'pay_ok' => '标记已付款完成',
            'receive_ok' => '标记已收款完成',
            'toUser' => '对用户',
            'common' => '留下评论',
            'good' => '好评',
            'good1' => '好评',
            'good2' => '差评',
            'submit_common' => '提交评论',
            'deal_question' => '交易遇到问题',
            'shensu_tip' => '如果对方未响应，不确认或者对交易条款发生纠纷，您可以申诉此交易',
            'shensu' => '申诉',
            'shensu_why' => '申诉原因',
            'shensu_why1' => '我已付款，但卖家未放行',
            'shensu_why2' => '卖家未遵守交易广告条款',
            'say_your_question' => '请简单描述你的问题',
        ),
    );
    return $language[$lang];
}