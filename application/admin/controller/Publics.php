<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
use extend\Encrypt;
/**
 * 后台公共控制器
 */
class Publics extends AdminBase{

	public function __construct(){
        parent::__construct();
        $this->view->engine->layout(false);
    }
    /**
     * 后台登录
     */
	public function login(){
        //如果已登录，就跳转转至主页
        if(session('islogin')){
            $this->redirect('index/index');
        }
		if(request()->isAjax()){
			$account  = trim(input('username'),' ');
			$password = trim(input('password'),' ');
			if(!$account){
                Api()->setApi('msg',"请输入用户名")->setApi('url',0)->ApiError();
            }elseif (!$password){
                Api()->setApi('msg',"请输入密码")->setApi('url',0)->ApiError();
            }
            $user_info = $this->getInfoByAccount($account);
            $this->checkUser($user_info);
            $authcode =  Encrypt::authcode($user_info['password'],'DECODE'); 
            if($password == $authcode){
        		session('islogin', $user_info['admin_id']);
                unset($user_info['password']);
                $user_info['last_login_time'] = time();
                session('user',$user_info);
                $this->updateDate($user_info);
                Api()->setApi('msg',"登录成功！")->setApi('url',url('Index/index'))->ApiSuccess();
        	}else{
        		 Api()->setApi('msg',"密码错误")->setApi('url',0)->ApiError();
        	}   
		}
		return view();
	}
	/**
	 * 获取用户信息
	 */
	public function getInfoByAccount($account){
		$info = model('admin')->where('account',$account)->find();
		if(is_object($info)){
			$info = $info->toArray();
			return $info;
		}else{
			Api()->setApi('msg',"用户不存在")->setApi('url',0)->ApiError();
		}
	}
	/**
	 * 检验用户及用户组状态 
	 */
	public function checkUser($user_info){
		if($user_info['status'] == 0){
			Api()->setApi('msg',"用户不存在")->setApi('url',0)->ApiError();
		}
		$group_status = model('AuthGroup')->where('group_id',$user_info['group'])->value('status');
		if($group_status == 0){
			Api()->setApi('msg',"用户所在用户组被禁用")->setApi('url',0)->ApiError();
		}
		return true;
	}
	/**
	 * 登录成功更新数据
	 */
    public function updateDate($user_info){
        //更新用户登录信息
        $loginStatus = $this->isMobile();//判断是否是 mobile 登录 or pc 登录
        $data = array(
            'admin_id'              => $user_info['admin_id'],
            'last_login_time'       => time(),
            'last_login_ip'         => get_client_ip(),
            'login_status'          => $loginStatus
        );
        model('admin')->save($data,['admin_id',$user_info['admin_id']]);
    }
    
    /**
     * 判断登陆设备
     */
    public function isMobile(){
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return 2;
        }else{
            return 1;
        }
    }

    /**
     * 退出登录
     */
    public function loginOut(){
    	$this->destroyUser();
        Api()->setApi('msg',"退出登录")->setApi('url',url('Publics/login'))->ApiSuccess();
    }
    /**
     * 退出前修改登录状态
     */
    public function changeLoginStatus(){

    }
}