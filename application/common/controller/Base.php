<?php
namespace app\common\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Log;
/**
 * 基础类
 * by chick 2017-05-02
 */
class Base extends Controller
{
	protected $base_url,$var;
    public function __construct(){
        parent::__construct();
    }
    public function _initialize(){
        $this->base_setUrl();
        $this->base_setVar();
        Session::init(['prefix' => MODULE_NAME,]);
    }

    /**
     * 设置常用url为常量
     *
     */
    final protected function base_setUrl(){
    	$this->base_url['module_name']     = Request::instance()->module();//当前模块
    	$this->base_url['controller_name'] = Request::instance()->controller();//当前控制器
    	$this->base_url['action_name']     = Request::instance()->action();//当前方法
    	$this->base_url['public_path']     = '/';
    	$this->base_url['static_path']     = '/static/';
    	foreach ($this->base_url as $name => $value) {
    		$name = strtoupper($name);
    		!defined($name) && define($name, $value);
    	}
        $this->base_url['js']  = '/theme/'.MODULE_NAME.'/static/js/';
        $this->base_url['css'] = '/theme/'.MODULE_NAME.'/static/css/';
        $this->base_url['img'] = '/theme/'.MODULE_NAME.'/static/img/';
        $this->assign($this->base_url);//加载到模版
    }

    final protected function base_setVar(){
    	$this->var['title']       = config('web_name');
    	$this->var['admin_title'] = config('web_admin_name')?:config('web_name').'后台管理系统';
        $this->var['nowpage']     = input(config('paginate.var_page'))?:1;
        $this->var['upload']      = ROOT_PATH.'public/upload/';
    	$this->assign($this->var);//加载到模版
    }
    
    /**
     * 判断URL链接前是否添加Http,/www.
     */
    public function getUrl($url){
        $p1 = '/^(.*?)www\.(.*)$/';
        $p2 = '/^http(.*)$/';
        $re1 = preg_match($p1,$url);
        if (!$re1){
            $url = $url;
        } else {
            $re2 = preg_match($p2,$url);
            if (!$re2){
                $url = 'http://'.$url;
            }
        }        
        return  $url;
    }
    
    /**
     * 登录判断
     */
    final protected function is_login(){
        $api    = new Api();
        $user_id = session('islogin');
        if (!$user_id) {
            return $api->setApi('msg','未登录')->ApiError();
        } else {
            return $api->setApi('msg','-')->ApiSuccess();
        }
    }
    /**
     * 清除登录信息
     */
    final protected function destroyUser(){
        session('islogin',0);
        session('user',[]);
    }


    
}
