<?php
namespace app\admin\controller;
use app\common\controller\Base;
use think\Controller;
use think\Session;
use extend\Auths;
use think\Url;
/**
 * 后台基本类
 * by wanggang 2017/7/10
 */
class AdminBase extends Base{
    protected $allowed = [
        'admin'=>[
            'publics.*',
        ],
    ];
	public function _initialize(){
        parent::_initialize();
        $this->setTitle();
        $this->isLogin();
        $this->_checkAuth();
    }
    private function _checkAuth(){
        $force = false;
        if (strtolower(CONTROLLER_NAME) == 'index' && strtolower(ACTION_NAME) == 'index') $force = true;
        !Auths::checkAuth([],$force) && Api()->setApi('msg',Auths::getMsg())->setApi('url',0)->ApiError();
    }
    protected function setTitle(){
        $m = ['pmenu'=>'后台管理','menu_name'=>'','menu_des'=>''];
        $url = Url::build(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        $menu = model('menu')->getByUrl($url);
        if ($menu) {
            $p = model('menu')->getByMenuId($menu->pid);
            if ($p) {
                $m['pmenu'] = $p['menu_name'];
            } else {
                $m['pmenu'] = '后台管理';
            }
            $m['menu_name'] = $menu->menu_name;
            $m['menu_des']  = $menu->menu_des;
        }
        $this->assign('menu',$m);
    }
    
    /**
     * 修改数据表指定字段
     * @param string    $table    要修改的数据表
     * @param string    $status   要修改成的值
     * @param int|array $id       主键ID
     * @param string    $pk       主键名，默认为表名_id
     * @param string    $field    要修改的字段，默认status
     */
    final protected function setStatus($table,$status,$id,$pk='',$field='status',$where='', $setAjax=false){
        $api    = Api('this',$setAjax);
        $pk     = $pk ?: $table.'_id';
        $field  = $field ?: 'status';
        $ids    = (array)$id;
        if (!$table  || !$ids || !$pk || !$field) {
            !$table      && $msg  = 'table';
            !$ids        && $msg  = 'id';
            !$pk         && $msg  = 'pk';
            !$field      && $msg  = 'field';
            return $api->setApi('msg','param error:'.$msg)->ApiWarning();
        }
        $model = model($table);
        foreach ($ids as $k => $id) {
            if (!(int)$id) continue;
            $where[$pk]    = (int)$id;
            $data[$field] = $status;
            $model->where($where)->update($data);
        }
        return $api->setApi('msg','操作成功')->ApiSuccess();
        
    }

    /**
     * 检测登录
     */
    protected function isLogin(){
        $module_name     = strtolower(MODULE_NAME);
        $controller_name = strtolower(CONTROLLER_NAME);
        $action_name     = strtolower(ACTION_NAME);
        if (array_key_exists($module_name,$this->allowed)) {
            $auth1 = $controller_name.'.*';
            $auth2 = $controller_name.'.'.$action_name;
            if (in_array($auth1, $this->allowed[$module_name])) {
                return;
            } elseif(in_array($auth2, $this->allowed[$module_name])) {
                return;
            }
        }
        $user_id = session('islogin');
        if (!$user_id) {
            $this->redirect('publics/login');
        }
    }

}
