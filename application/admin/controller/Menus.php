<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
use app\admin\Model\Menu;
/**
 * 菜单控制器
 * @author wanggang 
 * @version 2017/5/8
 */
class Menus extends AdminBase{
	/**
	 * 获取树状菜单列表
	 */
	public function getMenuTree($id = 0){
        $menus = Menu::all(function($db){
            $db->where(['status'=>['<>',-1]])->order('sort', 'asc');
        });
        resultToArray($menus);
        $select = getTree($menus,['primary_key'=>'menu_id','class_name'=>'form-control i-select','form_name'=>'pid'],2)->makeSelect($id,'menu_name',"顶级菜单");
        return $select;
    }
	/**
	 * 菜单列表
	 */
	public function index(){
        $menus = Menu::all(function($db){
	        $menu_name = input('keywords');
        	$where['status'] = ['<>',-1];
        	$db->where($where)->order('sort', 'asc');
        });
        resultToArray($menus);
        $tree = getTree($menus,['primary_key'=>'menu_id'])->makeTreeForHtml();
        return view('',
        	[
            'tree'=>$tree,
        ]);

	}
	/**
	 * 新增菜单
	 */
	public function add(){
		$pid = input('pid',0);
		$select =$this->getMenuTree($pid);
        $this->assign('select',$select);
		if (request()->isAjax()) {
			$data =input();
           	$re =model('Menu')->add_menu($data);
           	if($re >0){
           		Api()->setApi('url',url('Menus/index'))->ApiSuccess($re);
           	}else{
           		Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
           	}
        }
		return view();
	}
	/**
	 * 编辑菜单
	 */
	public function edit(){
		$menu_id=input('menu_id');
		$pid=Menu::get($menu_id)->pid;
		$select =$this->getMenuTree($pid);
		$menu_info = Menu::get($menu_id)->toArray();
		$this->assign('menu_info',$menu_info);
		$this->assign('select',$select);
		if(request()->isAjax()){
			$data =input();
			$re =model('Menu')->edit_menu($data);
			if($re >0 ){
				Api()->setApi('url',url('Menus/index'))->ApiSuccess($re);
			}else{
				Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
			}
		}
		return view();
	}
	/**
	 * 删除菜单
	 *
	 */
	public function del(){
		if(request()->isAjax()){
			$data =input();
			$re =model('Menu')->del_menu($data);
			if($re >0){
				Api()->setApi('url',url('Menus/index'))->ApiSuccess($re);
			}else{
				Api()->setApi('url',0)->ApiError();
			}
		}

	}
	/**
	 * 菜单图标
	 */
    public function fontawesome(){
        return view();
    }
     /**
      * 改变状态
      */
    public function changeStatus(){
        if(request()->isAjax()){
        	extract(input());
            $obj = $this->setStatus('menu',$status,$menu_id);
            if($obj->code){
                Api()->setApi('url',url('Menus/index'))->ApiSuccess();
            }else{
                Api()->setApi('url',0)->ApiError();
            }
        }
    }
}