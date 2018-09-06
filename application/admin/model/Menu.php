<?php
namespace app\admin\model;
use think\Model;
// use think\Validate;
use traits\model\SoftDelete;
/**
 * 菜单模型类
 * @author wanggang
 * @version 2017/5/8
 */
class Menu extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $readonly = [];//只读字段
	/**
	 * 菜单新增
	 */
	public function add_menu($data){
		if(array_key_exists('url_type', $data) && $data['url_type'] == 1){
				$data['url'] =url($data['module']."/".$data['controller']."/".$data['action']);
		}
		$result = $this->validate('menu.add')->save($data);
		if($result === false){
			return $this->getError();
		}else{
			return $result;
		}
	}
	/**
	 * 菜单编辑
	 */
	public function edit_menu($data){
		if(array_key_exists('url_type', $data) && $data['url_type'] == 1){
			$data['url'] =url($data['module']."/".$data['controller']."/".$data['action']);
		}
		$result = $this->validate('menu.edit')->save($data,['menu_id'=>$data['menu_id']]);
		if($result === false){
			return $this->getError();
		}else{
			return $result;
		}
	}
	public function del_menu($data){
		$id = $data['id'];
		if(is_array($id)){
			$menu_id = $id;
		}else{
			$menu_id = array($id);
		}
		$menu_id = $this->getMenuIds($menu_id);
		$re =$this->destroy($menu_id);
		return $re;



	}

	public function change_status($data){
        unset($data['location']);
        $result = $this->save($data,['menu_id'=>$data['menu_id']]);
        if($result === false){
            return $this->getError();
        }else{
            return $result;
        }
    }
    protected function getMenuIds(&$menu_id){
        $menu_ids = $this->where(['pid'=>['in',$menu_id]])->column('menu_id');
        $menu_ids= array_flip(array_flip($menu_ids));
        if ($menu_ids) {
            $this->getMenuIds($menu_ids);
        }
        $menu_id = array_merge($menu_id,$menu_ids);
        $menu_id = array_flip(array_flip($menu_id));
        return $menu_id;
    }
}