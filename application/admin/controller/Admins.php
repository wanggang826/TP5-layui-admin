<?php
namespace app\admin\controller;
use think\Image;
use think\Session;
use extend\Encrypt;
use think\Log;
/**
 * 管理员控制器
 * @author  wanggang
 * @version 2017/5/12
 */
class Admins extends AdminBase{
    public function defaluts(){
        $this->redirect(url('Admins/index'));
    }
    /**
     * 管理员列表
     */
    public function index(){
        $authGroup =model('AuthGroup')->where('group_id != 1')->select();
        resultToArray($authGroup);
        $data=input();
        $admins = model('Admin')->select_admin($data);
        return view('',[
            'admins'=>$admins,'authGroup'=>$authGroup,
        ]);
    }
    /**
     * 编辑管理员
     */
    public function edit(){
    	$authGroup =model('AuthGroup')->where('group_id != 1')->select();
    	resultToArray($authGroup);
    	$admin_id=input('admin_id');
    	$admin_info = model('admin')->where('admin_id',$admin_id)->find()->toArray();
        $page = input('page');
    	if (request()->isAjax()) {
            $data=input();
           	$re =model('Admin')->edit_admin($data);
           	if($re >0){
           		Api()->setApi('url',url('Admins/index',['page'=>input('page')]))->ApiSuccess($re);
           	}else{
           		Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
           	}
        }
    	return view('',
    		['authGroup'=>$authGroup,'admin_info'=>$admin_info,'page'=>$page,]
    	);
    }
    /**
     * 新增管理员
     */
    public function add(){
    	$authGroup =model('AuthGroup')->where('group_id != 1')->select();
    	resultToArray($authGroup);
    	if (request()->isAjax()) {
    		$data =input();
       	    $re =model('Admin')->add_admin($data);
           	if($re >0){
           		Api()->setApi('url',url('Admins/index'))->ApiSuccess($re);
           	}else{
           		Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
           	}
        }
    	return view('',['authGroup'=>$authGroup,]);
    }

    /**
     * 删除管理员
     */
    public function del(){
      if(request()->isAjax()){
        $time = time();
        $data = input();
        if (in_array(1,(array)$data['id'])) {
            Api()->setApi('url',0)->setApi('msg','超级管理员不能删除')->ApiError();
        }
        $obj =$this->setStatus('admin',$time,$data['id'],'','delete_time');
        if(1 == $obj->code){
            $obj->setApi('url',input('location'))->apiEcho();
        }else{
            $obj->setApi('url',0)->apiEcho();
        }
      }
    }

    /**
     * 修改密码
     */
    public function admin_password(){
       if(request()->isAjax()){
           $data=input();
           if($data['new_password'] == $data['confirm_password']) {//两次密码是否一致
               $password= model('admin')->where(['admin_id'=>$data['admin_id']])->field('password')->find()->toArray();//提取原密码
               //原密码解密
               $old_password  = Encrypt::authcode( $password['password'],'DECODE');
               $original_password = $data['original_password'];
               if ($old_password == $original_password) {//查看原密码是否输入正确
                   $re = model('admin')->password_edit($data);
                   if ($re) {
                       Api()->setApi('url', url('', ['page' => input('page')]))->ApiSuccess();
                   } else {
                       Api()->ApiError();
                   }
               } else {
                   Api()->setApi('msg', '原密码有误!')->setApi('url', 0)->ApiError();
               }
           }else{
               Api()->setApi('msg', '两次密码不一致!')->setApi('url', 0)->ApiError();
           }
       }else{
           $admin_id = Session::get('islogin');
       }
        return view('',['admin_id'=>$admin_id]);
    }

    /**
     * 后台用户密码重置
     */
    public function reset_password(){
        if(request()->isAjax()){
            $data=input();
            if(session('islogin') == 1){
                if($data['new_password'] == $data['confirm_password']) {//两次密码是否一致
                    $re = model('admin')->password_edit($data);
                    if ($re) {
                        Api()->setApi('url', url('admins/index', ['page' => input('page')]))->ApiSuccess();
                    } else {
                        Api()->ApiError();
                    }
                }else{
                    Api()->setApi('msg', '两次密码不一致!')->setApi('url', 0)->ApiError();
                }
            }else{
                Api()->setApi('msg', '非超级管理员不可进行该操作!')->setApi('url', 0)->ApiError();
            }
        }else{
            $admin_id = input('admin_id');
        }
        return view('',['admin_id'=>$admin_id]);
    }
}
