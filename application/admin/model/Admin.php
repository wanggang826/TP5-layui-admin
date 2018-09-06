<?php
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;
use extend\Encrypt;
/**
 * 管理员模型
 * @author 
 * @version wanggang 2018/7/11
 */
class Admin extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $readonly = [];//只读字段

    /**
     * 关联查询|关联用户组表
     */
    public function AuthGroup(){
        return $this->hasOne('AuthGroup','group_id','group')->field('group_name');
    }
    
    /**
     * 管理员列表查询
     */
    public function select_admin($data,$where=array(),$base = array('status'=>array('egt',0))){
    	if(isValue($data,'nickname')){
			$where['nickname'] =['like','%'.(string)$data['nickname'].'%'];
		}
        $query =$data;
		$where = array_merge( (array)$base, /*$REQUEST,*/ (array)$where);
		$list=$this->where($where)->order('admin_id asc')->paginate('',false,['query' => $query]);
		resultToArray($list);
		return $list;
    }
    /**
     * 新增管理员
     */
    public function add_admin($data){
        if($data['password'] != false){
            $data['password'] = Encrypt::authcode($data['password'],'ENCODE'); //加密
        }
    	$result = $this->validate('admin.add')->save($data);
		if($result === false){
			return $this->getError();
		}else{
			return $result;
		}
    }
    /**
     * 编辑管理员
     */
    public function edit_admin($data){
        unset($data['page']);
    	$result = $this->validate('admin.edit')->save($data,['admin_id'=>$data['admin_id']]);
		if($result === false){
			return $this->getError();
		}else{
			return $result;
		}
    }
    /*
     * 管理员修改密码
     * */
    public function password_edit($data){
        $pass['admin_id'] = $data['admin_id'];
        $pass['password'] = Encrypt::authcode($data['confirm_password'],'ENCODE');
        $result = $this->save($pass,['admin_id'=>$pass['admin_id']]);
        if($result === false){
            return $this->getError();
        }else{
            return $result;
        }
    }

}