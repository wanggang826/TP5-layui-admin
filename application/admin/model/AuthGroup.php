<?php
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;
/**
 * 用户组模型
 * @author 
 * @version wanggang 2017/5/12
 */
class AuthGroup extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $readonly = [];//只读字段
    /**
     * 用户组列表查询
     */
    public function select_group($data,$where=array()){
    	if(isValue($data,'group_name')){
			$where['group_name'] =['like','%'.(string)$data['group_name'].'%'];
		}
		$query= $data;
		$list=$this->where($where)->order('group_id asc')->paginate('',false,['query' => $query]);
		resultToArray($list);
    	return $list;
    }
    /**
     * 新增用户组
     */
    public function add_group($data){
    	$result = $this->validate('AuthGroup.add')->save($data);
		if($result === false){
			return $this->getError();
		}else{
			return $result;
		}
    }
    /**
     * 编辑用户组
     */
    public function edit_group($data){
    	$result = $this->validate('AuthGroup.edit')->save($data,['group_id'=>$data['group_id']]);
		if($result === false){
			return $this->getError();
		}else{
			return $result;
		}
    }
}