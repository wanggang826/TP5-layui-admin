<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
use app\admin\Model\Member;
use extend\Upload;
use think\Request;
use think\Session;
/**
 * 管理员控制器
 * @author  wanggang
 * @version 2017/5/12
 */
class User extends AdminBase{
    public function defaluts(){
        $this->redirect(url('User/index'));
    }
    /**
     * 会员列表
     */
    public function index(){
    	$members = model('member')->select_member(input());
    	return view('',['members'=>$members]);
    }
    /**
     * 会员详情
     */
    public function detail(){
    	$info = model('member')->getinfoByid(input('user_id'));
       
    	return view('',['info'=>$info]);
    }
    /**
     * 实名认证
     */
    public function apply_list(){
    	$lists = model('member')->select_member(input());
    	return view('',['lists'=>$lists]);
    }
    /**
     * 删除
     */
    public function del(){
    	if(request()->isAjax()){
	        $time = time();
	        $data = input();
	        $obj =$this->setStatus('member',$time,$data['id'],'user_id','delete_time');
	        if(1 == $obj->code){
	            $obj->setApi('url',input('location'))->apiEcho();
	        }else{
	            $obj->setApi('url',0)->apiEcho();
	        }
	    }
    }
    /**
     * 审核
     */
    public function user_check(){
    	if(request()->isAjax()){
	        $time = time();
	        $data = input();
            if($data['is_agree'] == 1){
                $user_isagree = model('member')->where('user_id',$data['id'])->value('is_agree');
                if($user_isagree == 1){
                    Api()->setApi('msg','该认证已经通过，不可重复操作')->setApi('url',0)->ApiError();
                }
            }
	        $obj2 =$this->setStatus('member',$data['is_agree'],$data['id'],'user_id','is_agree');
	        $obj1 =$this->setStatus('member',1,$data['id'],'user_id','is_check');
	        $obj3 =$this->setStatus('member',session('islogin'),$data['id'],'user_id','check_user_id');
	        $obj4 =$this->setStatus('member',time(),$data['id'],'user_id','check_time');
	        if(1 == $obj1->code && 1==$obj2->code){
	            $obj1->setApi('url',input('location'))->apiEcho();
	        }else{
	            $obj1->setApi('url',0)->apiEcho();

	        }
	    }
    }
    /**
     * 改变vip
     */
    public function is_vip(){
        $obj =$this->setStatus('member',input('is_vip'),input('id'),'user_id','is_vip');
        if(1 == $obj->code){
            $obj->setApi('url',input('location'))->apiEcho();
        }else{
            $obj->apiEcho();
        }
    }
}