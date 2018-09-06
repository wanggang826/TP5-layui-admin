<?php
namespace extend;
use think\Request;
use think\Session;
use think\Loader;
use think\Config;
use think\Url;
/**
 * 权限验证类
 * @author  chick
 * @version 2017.05.18
 * 用于thinkphp 5.0 以上版本
 * 数据库结构
 * 用户表      user_model     ---    user_pk,user_group,status_field(用户表主键,所属用户组,状态)
 * 用户组表    group_model    ---    group_pk，status_field(用户组表主键,状态)
 * 菜单表      menu_model     ---    menu_pk,module,controller,action(菜单表主键节点表和菜单表为同一张表)
 * 节点权限表  auth_model     ---    node_pk,group_pk(节点表主键，用户组表主键)
 */
class Auths
{
	private static $_uid,$_user,$_module,$_controller,$_action,$_msg;//声明变量
	private static $_config = [
		'user_model'           => 'admin',//用户表
		'user_pk'              => 'admin_id',//用户表主键
		'user_group'           => 'group',//用户表用户组ID字段
		'group_model'          => 'auth_group',//用户组表
		'group_pk'             => 'group_id',//用户组表主键
		'menu_model'           => 'menu',//菜单表
		'menu_pk'              => 'menu_id',//菜单表主键
		// 'menu_field'           => 'menu_id as id,pid as pId,menu_name as name,url as href,menu_icon',
		// 'menu_order'           => 'sort asc',
		'module'               => 'module',//节点--module字段名
		'controller'           => 'controller',//节点--controller字段名
		'action'               => 'action',//节点--action字段名
		'auth_model'           => 'auth',//节点权限表
		'status_field'         => 'status',//状态字段
		'default_status'       => 1,//正常状态
		'administrator'        => [1,],//超管用户表主键值,可设置多个
		'session_islogin'      => 'islogin',//session里的登录状态,保存的用户ID
		'session_user'         => 'user',//session里保存的当前登录用户信息
		'check_scene'          => 'aways',//验证场景once--一次 aways--每次
		'public'               => [ 'admin' => ['publics.*','index.*'], ],//不需要权限验证的控制器或方法
	];

	/**
	 * 获取信息
	 */
	public static function getMsg(){
		return self::$_msg;
	}

	/**
	 * 权限检查
	 *
	 * @return [type] [description]
	 */
	public static function checkAuth($config=[],$force = false){
		self::_init($config);
		$uid  = &self::$_uid;
		$user = &self::$_user;
		if ($uid && self::_isAdministrator()) {//超级管理员查出所有节点
			self::_getMenu(true,$force);
			return true;
		}
		if (self::_isPublic()) {
			$uid && self::_getMenu(false,$force);
			return true;
		}
		if (!self::_checkUser() || !self::_checkUserGroup()) return false;
		if(self::_checkAuth($force)){
			return true;
		}

		return false;

	}

	/**
	 * 初始化
	 */
	private static function _init($config = [])
	{
		self::$_config                = array_merge(self::$_config,$config);
		self::$_config['check_scene'] = Config::get('check_scene');
		extract(self::$_config);
		self::$_uid                   = Session::get($session_islogin,MODULE_NAME);
		self::$_user                  = Session::get($session_user,MODULE_NAME);
		self::$_module                = Request::instance()->module();
		self::$_controller            = Request::instance()->controller();
		self::$_action                = Request::instance()->action();
	}

	/**
	 *
	 */
	private static function _checkAuth($force = false){
		extract(self::$_config);
		$menu_list = self::_getMenu($force);
		$auth = self::$_module.'/'.self::$_controller.'/'.self::$_action;
		switch ($check_scene) {
			case 'once'://验证一次(登录验证)
				if (!in_array(strtolower($auth), $menu_list['auth'],true)) {
					self::_setMsg('访问页面未授权');
					return false;
				}
				self::_setMsg('once');
				return true;
				break;
			case 'aways'://总是验证
			default:
				$url = Url::build($auth);
				$group_id = self::$_user[$user_group];
				$auth_model = Loader::model($auth_model);
				$menu_model = Loader::model($menu_model);
				$menu_id = $menu_model->where(['url'=>$url])->value($menu_pk);
				if (!$menu_id) {
					self::_setMsg('访问页面不存在');
					return false;
				}
				$auth_count = $auth_model->where([$menu_pk=>$menu_id,$group_pk=>$group_id])->count();
				if ($auth_count < 1) {
					self::_setMsg('访问页面未授权');
					return false;
				}
				self::_getMenu(false,$force);
				self::_setMsg('aways');
				return true;
				break;
		}
		return false;//避免非法数据
	}
	/**
	 * 获取所有权限节点
	 */
	private static function _getAuth($getAll = false,$force = false){
		$uid  = &self::$_uid;
		$user = &self::$_user;
		if (!$uid) {
			self::_setMsg('登录状态不正确');
			return false;
		}
		extract(self::$_config);
		if (!Session::has($session_user.'.nodeList') || $force) {
			$auth_model = Loader::model($auth_model);
			$menu_model = Loader::model($menu_model);
			if ($getAll === true) {
				$node_list   = $menu_model->column($menu_pk);
			} else {
				$node_list   = $auth_model->where($group_pk,$user[$user_group])->column($menu_pk);
			}

			Session::set($session_user.'.nodeList',serialize($node_list));
		} else {
			$node_list = unserialize(Session::get($session_user.'.nodeList'));
		}
		return $node_list;
	}
	/**
	 * 获取所有菜单
	 */
	private static function _getMenu($getAll = false,$force = false){
		extract(self::$_config);
		if (!Session::has($session_user.'.menuList') || $force) {
			$node = self::_getAuth($getAll,$force);
			$menu_model = Loader::model($menu_model);
			$menu_list  = $menu_model
						->where([$menu_pk=>['in',$node]])
						->field($menu_pk.','.$module.','.$controller.','.$action)
						->select();
			if ($menu_list) {
				foreach ($menu_list as &$result) {
			        $result = $result->getData();
			        $results[$menu_pk][]   = $result[$menu_pk];
			        $results['auth'][]     = strtolower($result[$module].'/'.$result[$controller].'/'.$result[$action]);
			    }
			    unset($menu_list);
			    $menu_list = $results;
			} else {
				$menu_list = [];
			}
			Session::set($session_user.'.menuList',serialize($menu_list));
			Session::delete($session_user.'.nodeList');
		} else {
			Session::delete($session_user.'.nodeList');
			$menu_list = unserialize(Session::get($session_user.'.menuList'));
		}
		return $menu_list;
	}
	/**
	 * 验证用户状态
	 * 禁用返回false
	 */
	private static function _checkUser(){
		extract(self::$_config);
		if (!self::$_uid || self::$_uid != self::$_user[$user_pk]) {
			self::_setMsg('登录状态不正确');
			return false;
		}
		$user_model = Loader::model($user_model);
		$status      = $user_model->where([$user_pk=>self::$_uid])->value($status_field);
		if ($status != $default_status) {//非正常状态
			self::_setMsg('用户非可用状态');
			return false;
		}
		return true;
	}
	/**
	 * 验证用户组状态
	 * 禁用返回false
	 */
	private static function _checkUserGroup(){
		$uid  = &self::$_uid;
		$user = &self::$_user;
		extract(self::$_config);
		if (!$user[$user_group]) {
			self::_setMsg('用户未授权，请联系管理员');
			return false;
		}
		$group_model = Loader::model($group_model);
		$status       = $group_model->where([$group_pk=>$user[$user_group]])->value($status_field);
		if ($status != $default_status) {
			self::_setMsg('用户组非可用状态');
			return false;
		}
		return true;
	}

	/**
	 * 验证是否公开控制器
	 */
	private static function _isPublic(){
		extract(self::$_config);
		$_module     = strtolower(self::$_module);
		$_controller = strtolower(self::$_controller);
		$_action     = strtolower(self::$_action);
		if (array_key_exists($_module, $public)) {
			$module_pub = $public[$_module];
		}
		if(isset($module_pub)){
			$alloweds = $_controller.'.*';
			$allowed = $_controller.'.'.$_action;
			if (in_array($alloweds, $module_pub)) {
				self::_setMsg('公共控制器');
				return true;
			} else if (in_array($allowed, $module_pub)) {
				self::_setMsg('公共方法');
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 */
	private static function _isAdministrator(){
		extract(self::$_config);
		if ($administrator && in_array(self::$_uid, $administrator)){//超级管理员
			self::_setMsg('超级管理员帐号');
			return true;
		}
		return false;
	}

	/**
	 * 设置信息
	 */
	private static function _setMsg($msg){
		self::$_msg = $msg;
		return null;
	}
}