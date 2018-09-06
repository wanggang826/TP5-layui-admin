<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Image;
use think\Session;
use think\Url;
use think\Request;
use think\Db;
// 应用公共文件

function version($versionDec)
{
    $versionBin = sprintf("%016b", $versionDec);
    $version = "";
    for ($i = 0; $i < 4; $i++) {
        $start = $i * 4;
        $version .= bindec(substr($versionBin, $start, 4));
        $version .= ".";
    }
    $version = rtrim($version, ".");
    return $version;
}

function voltage($voltage) 
{
    return forBase1("voltage", 16, $voltage);
}

function temperature($temperature) 
{
    return forBase("temperature", 6, $temperature);
}

function forBase($name, $max, $data) 
{
    $dataArray = array();
    for ($i = 1; $i <= $max; $i++) {
        $start = 4 * ($i - 1);
        $named = "$name$i";
        $$named = substr($data, $start, 4);
        $$named = hexdec($$named);
        $dataArray[$named] = ($$named-2731)/10;
    }
    return $dataArray;
}
function forBase1($name, $max, $data)
{
    $dataArray = array();
    for ($i = 1; $i <= $max; $i++) {
        $start = 4 * ($i - 1);
        $named = "$name$i";
        $$named = substr($data, $start, 4);
        $$named = hexdec($$named);
        $dataArray[$named] = $$named;
    }
    return $dataArray;
}
function system_status_1($systemStatus1)
{
    $systemStatus1 = sprintf("%016b", $systemStatus1);
    $systemStatus1Array = array();
    $systemStatus1Array['highVolage'] = substr($systemStatus1, 15, 1);
    $systemStatus1Array['lowVolage'] = substr($systemStatus1, 14, 1);
    $systemStatus1Array['packHigh'] = substr($systemStatus1, 13, 1);
    $systemStatus1Array['packLow'] = substr($systemStatus1, 12, 1);
    $systemStatus1Array['packFull'] = substr($systemStatus1, 11, 1);
    $systemStatus1Array['charging'] = substr($systemStatus1, 10, 1);
    $systemStatus1Array['discharge'] = substr($systemStatus1, 9, 1);
    $systemStatus1Array['shortOut'] = substr($systemStatus1, 8, 1);
    $systemStatus1Array['batteriesCharging'] = substr($systemStatus1, 7, 1);
    $systemStatus1Array['batteriesDischarge'] = substr($systemStatus1, 6, 1);
    $systemStatus1Array['envLow'] = substr($systemStatus1, 5, 1);
    $systemStatus1Array['envHigh'] = substr($systemStatus1, 4, 1);
    $systemStatus1Array['mos'] = substr($systemStatus1, 3, 1);
    $systemStatus1Array['chargerBackup'] = substr($systemStatus1, 2, 1);
    $systemStatus1Array['limitingCurrent'] = substr($systemStatus1, 1, 1);
    $systemStatus1Array['samping'] = substr($systemStatus1, 0, 1);
    return $systemStatus1Array;
}

function system_status_2($systemStatus2) 
{
    $systemStatus2 = sprintf("%016b", $systemStatus2);
    $systemStatus2Array = array();
    $systemStatus2Array['limitingCurrentIndicate'] = substr($systemStatus2, 15, 1);
    $systemStatus2Array['chargingMosOpen'] = substr($systemStatus2, 14, 1);
    $systemStatus2Array['dischargeMosOpen'] = substr($systemStatus2, 13, 1);
    $systemStatus2Array['packPowerUp'] = substr($systemStatus2, 12, 1);
    $systemStatus2Array['callPolice'] = substr($systemStatus2, 11, 1);
    $systemStatus2Array['alarmSound'] = substr($systemStatus2, 10, 1);
    $systemStatus2Array['chargingMosClose'] = substr($systemStatus2, 9, 1);
    $systemStatus2Array['dischargeMosClose'] = substr($systemStatus2, 8, 1);
    return $systemStatus2Array;
}

function system_status_3($systemStatus3)
{
    $systemStatus3 = sprintf("%016b", $systemStatus3);
    $systemStatus3Array = array();
    $systemStatus3Array['dischargeMosFailure'] = substr($systemStatus3, 15, 1);
    $systemStatus3Array['chargingMosFailure'] = substr($systemStatus3, 14, 1);
    $systemStatus3Array['batteriesFault'] = substr($systemStatus3, 13, 1);
    $systemStatus3Array['temperatureNtcFault'] = substr($systemStatus3, 12, 1);
    $systemStatus3Array['heatingFilmFault'] = substr($systemStatus3, 11, 1);
    $systemStatus3Array['heatingFileWork'] = substr($systemStatus3, 10, 1);
    $systemStatus3Array['dischargeInstructions'] = substr($systemStatus3, 9, 1);
    $systemStatus3Array['chargingInstructions'] = substr($systemStatus3, 8, 1);
    return $systemStatus3Array;
}

function tcp_client($domain, $port, $data) {
    $client = new swoole_client(SWOOLE_SOCK_TCP);

    //连接到服务器
    if (!$client->connect($domain, $port, 0.5)) {
        die("connect failed.");
    }

    //向服务器发送数据
    if (!$client->send("$data\r\n")) {
        die("send failed.");
    }

    //关闭连接
    $client->close();
}
/*
 * $name  string    为type="file"的input框的name值
 * $file string     存在图片的文件夹 (文件夹必须在upload之下)
 * return  string   返回图片的文件夹和名字
 */
function upload_img($name,$file){
    $up_dir = "./public/upload/$file";
    if (!file_exists($up_dir)) {
        mkdir($up_dir, 0777, true);
    }
    $image = Image::open(request()->file($name));//打开上传图片
    $size = input('avatar_data');//裁剪后的尺寸和坐标
    $size_arr=json_decode($size,true);
    $type= substr($_FILES[$name]['name'],strrpos($_FILES[$name]['name'],'.')+1);
    $name = time().".".$type;
    $info =$image->crop($size_arr['width'], $size_arr['height'],$size_arr['x'],$size_arr['y'])->save("./public/upload/$file/$name");
    if($info){
        return $file."/".$name;
    }else{
        return false;
    }
}
// 应用公共文件
function resultToArray(&$results){
    foreach ($results as &$result) {
        $result = $result->getData();
    }
}

function getTree($data,$options=[],$level=0){
    return new \extend\Tree($data,$options,$level);
}
function Api($type = '',$setApi=false){
    $app_debug = config('app_debug');
    $api = new \app\common\controller\Api($app_debug);
    return $api->setType($type,$setApi);
}
/**
 * 判断值是否为空
 */
function isValue($data,$key=false){
    if ($key !== false) {
        if(!is_array($data)) return false;
        if(!array_key_exists($key,$data)) return false;
        $v = $data[$key];
    } else {
        $v = $data;
    }
    if ($v === 0 || $v === '0') return true;
    if($v != '') return true;
    if (is_array($v) && $v !=[]) return true;
    return false;
}
function getNamebyPk($model,$pk_name,$getField,$pk_value){
    $data = model($model)->where([$pk_name=>$pk_value])->find();
    if($data){
        return  model($model)->where([$pk_name=>$pk_value])->find()->$getField;
    }else{
        return '--';
    }

}

/**
 * 获取图片用于显示
 */
function getImg($imgName,$isUrl=false){
    if ($isUrl) {
        $url = $imgName;
    } else {
        $url   = config('STATIC_URL').'/upload/'.$imgName;
        $url_t   = ROOT_PATH.'public/upload/'.$imgName;
    }
    if (!is_file($url_t)) {
        $url = config('static_url').'/upload/'.config('default_img');
        $url_t = ROOT_PATH.'public/upload/default.png';
        $url = is_file($url_t) ? $url : ROOT_PATH.'/public/upload/default.png';
    }
    return $url;
}

function get_login_user_name(){
    return session('user.nickname') ?:session('user.account');
}
function get_login_admin_group(){
    $group = session('user.group');
    if (!$group) { return;}
    $name = model('auth_group')->where(['group_id'=>$group])->value('group_name');
    return $name;
}

function buildRandomString($type=1,$length=4){
    if($type == 1){
        $chars = join("",range(0,9));
    }elseif($type == 2){
        $chars = join("",array_merge(range("a","z"),range("A","Z")));
    }elseif($type == 3){
        $chars = join("",array_merge(range("a","z"),range("A","Z"),range(0,9)));
    }
    $chars = str_shuffle($chars);
    return substr($chars,0,$length);
}

/**
 * tp5废弃的字母函数
 * @version 2017/08/28 [by iwater]
 */
function C($name = '', $value = null, $range = ''){
    return config($name, $value, $range );
}
function D($name = '', $layer = 'model', $appendSuffix = false){
    return model($name, $layer, $appendSuffix);
}
function M($name = '', $config = [], $force = true){
    return db($name, $config, $force);
}
function U($url = '', $vars = '', $suffix = true, $domain = false){
    return url($url, $vars, $suffix, $domain);
}
function W($name, $data = []){
    return widget($name, $data);
}
function I($key = '', $default = null, $filter = ''){
    return input($key, $default, $filter);
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 随机纯数字字符串
 * @param  [number] $length [字符串长度]
 * @return [string]         [字符串]
 * wanggang
 */
function make_code($length){
    $output='';
    for ($i = 0; $i < $length; $i++) {
        $output .= rand(0, 9); //生成php随机数
    }
    return $output;
}

/**
 * 生成订单
 * @param string $header [description]
 * @return [str] [description]
 * @author [iwater]  2017/09/28
 */
function set_order($header = 'kwd'){
    $order_no = $header.time().rand(0,9).rand(0,9);
    return $order_no;
}


if (!function_exists('urldo')) {
    /**
     * Url生成
     * @param string        $url 路由地址
     * @param string|array  $vars 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @return string
     */
    function urldo($url = '', $vars = '', $suffix = true, $domain = true)
    {
        return Url::build($url, $vars, $suffix, $domain);
    }
}


//获取分页参数设置
function _pageconfig($listRows){
    config(['paginate'=>['type'      => 'bootstrap','list_rows' => $listRows,'var_page'  => 'page',]]);
    Session::set('pageSize', config('paginate.list_rows'));
}

function birthday1($age){
    // $age = strtotime($birthday);
    if($age === false){
        return false;
    }
    list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
    $now = strtotime("now");
    list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now));
    $age = $y2 - $y1;
    if((int)($m2.$d2) < (int)($m1.$d1))
        $age -= 1;
    return $age;
}
function birthday($birthday){
    list($year,$month,$day) = explode("-",$birthday);
    $year_diff = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff  = date("d") - $day;
    if ($day_diff < 0 || $month_diff < 0)
        $year_diff--;
    return $year_diff;
}
//转换ueditor html to string
function html_to_str($str){
    $str  = strip_tags(html_entity_decode($str));
    $qian = array(" ","　","\t","\n","\r");
    $hou  = array("","","","","");
    $str = str_replace($qian,$hou,$str);
    return $str;
}

//1、Unix时间戳转日期
function unixtime_to_date($unixtime, $timezone = 'PRC') {
    $datetime = new DateTime("@$unixtime"); //DateTime类的bug，加入@可以将Unix时间戳作为参数传入
    $datetime->setTimezone(new DateTimeZone($timezone));
    return $datetime->format("Y-m-d H:i:s");
}

//2、日期转Unix时间戳
function date_to_unixtime($date, $timezone = 'PRC') {
    $datetime= new DateTime($date, new DateTimeZone($timezone));
    return $datetime->format('U');
}
/**
 * 获取配置文件值
 * @param  [type] $config_mark [description]
 * @return [type]              [description]
 */
function getconfigs($config_mark){
    $where['config_mark'] = $config_mark;
    return db('config')->where($where)->value('config_value');
}


/**
 * 验证手机号码格式
 * @param  string $mobile [description]
 * @return [type]         [description]
 */
function checkMobile( $mobile = ''){
    if( !preg_match("/^1[34578]\d{9}$/", $mobile)){
        return false;
    }else{
        return true;
    }
}

function getAlarmType($type){
    switch ($type) {
        case '1':
            $alarm_type = '过压';
            break;
        case '2':
            $alarm_type = '欠压';
            break;
        case '3':
            $alarm_type = '过温';
            break;
        default:
            $alarm_type = '不明';
            break;
    }
    return $alarm_type;
}