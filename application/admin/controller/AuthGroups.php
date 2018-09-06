<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
use app\admin\Model\AuthGroup;
/**
 * 管理员控制器
 * @author  wangang 
 * @version 2017/5/13
 */
class AuthGroups extends AdminBase{
	/**
	 * 用户组列表
	 */
	public function index(){
		$data =input();
		$groups =model('AuthGroup')->select_group($data);
		return view('',[
			'groups'=>$groups,
			]);
	}
	/**
	 * 新增用户组
	 */
	public function add(){
		if(request()->isAjax()){
			$data =input();
			$re =model('AuthGroup')->add_group($data);
			if($re >0){
				Api()->setApi('url',url('AuthGroups/index'))->ApiSuccess($re);
			}else{
				Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
			}
		}
		return view();
	}
	/**
	 * 编辑用户组
	 */
	public function edit(){
		$group_id =input('group_id');
		$group_info =model('authGroup')->where('group_id',$group_id)->find()->toArray();
		$page =input('page');
		if(request()->isAjax()){
			$data =input();
			unset($data['page']);
			$re =model('AuthGroup')->edit_group($data);
			if($re >0){
				Api()->setApi('url',url('AuthGroups/index',['page'=>input('page')]))->ApiSuccess($re);
			}else{
				Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
			}
		}
		return view('',['group_info'=>$group_info,'page'=>$page,]);
	}
	/**
	 * 删除用户组
	 */
	public function del(){
		if(request()->isAjax()){
			$data =input();
			$time =time();
			if (in_array(1,(array)$data['id'])) {
	            Api()->setApi('url',0)->setApi('msg','超级管理员不能删除')->ApiError();
	        }
	        if(is_array($data['id'])){
	        	foreach ($data['id'] as $key => $value) {
					$admins = model('admin')->where(['group'=>$value])->count();
					if($admins != 0){
						Api()->setApi('url',0)->setApi('msg','#'.$value.'用户组存在用户')->ApiError();
					}
				}
	        }else{
	        	$admins = model('admin')->where(['group'=>$data['id']])->count();
				if($admins != 0){
					Api()->setApi('url',0)->setApi('msg','#'.$value.'用户组存在用户')->ApiError();
				}
	        }
	        $obj =$this->setStatus('AuthGroup',$time,$data['id'],'group_id','delete_time');
	        if(1 == $obj->code){
	            Api()->setApi('url',input('location'))->ApiSuccess();
	        }else{
	            Api()->setApi('url',0)->ApiError();
	        }
	    }
	}
	/**
     * 改变状态：启用|禁用
     */
    public function change_status(){
    	$obj =$this->setStatus('AuthGroup',input('status'),input('group_id'),'group_id');
    	if($obj->code){
    		Api()->setApi('url',input('location'))->ApiSuccess();
    	}else{
    		Api()->ApiError();
    	}
    }
	/**
	 * 设置用户组权限
	 */
	public function setAuth(){
		$menus = model('Menu')->order('sort', 'asc')->select();
        resultToArray($menus);
        $tree = getTree($menus,['primary_key'=>'menu_id'],2)->makeTreeForHtml();
        $group_id=input('group_id');
        $groupAuth = model('Auth')->where(['group_id'=>$group_id])->column('menu_id');
        if(request()->isAjax()){
        	// dump(input());die;
        	extract(input());
        	$list =['group_id'=>$group_id];
        	// sort($menu_ids);
        	model('Auth')->where($list)->delete();
        	$auth =array();
        	if(array_key_exists('menu_ids',input())){
        		foreach ($menu_ids as $key => $v) {
	        		$auth[$key]['group_id']=$group_id;
	        		$auth[$key]['menu_id']=$v;
	        	}
        	}
        	$re =model('Auth')->saveAll($auth);
        	if(count($re) >= 0){
	            Api()->setApi('url',url('AuthGroups/index'))->ApiSuccess($re);
	        }else{
	            Api()->setApi('url',0)->ApiError();
	        }
        }
		return view('',[
			'tree'=>$tree,
			'menu_model'=>model('menu'),
			'groupAuth'=>$groupAuth,
			]);
	}
}